<?php

namespace Stego\Tasks\Fs;

use Stego\Stubs\TestConfiguration;

class CleanTaskTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        if (!function_exists('\Stego\service')) {
            require getcwd() . '/src/functions.php';
        }
        if (!function_exists('assertTrue')) {
            require getcwd() . '/vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';
        }
    }

    public function testCleanTask()
    {
        $files = array();
        foreach (range(0, 10) as $_) {
            $file = tempnam(sys_get_temp_dir(), rand(0, 1000));
            touch($file);
            $this->assertTrue(file_exists($file));
            $files[] = $file;
        }

        $mockedConsole = $this->getMockBuilder('\Stego\Console\Stdio\Console')
            ->disableOriginalConstructor()
            ->getMock();

        $mockedConsole->expects($this->any())
            ->method('write')
            ->willReturnCallback(function ($msg) use ($files) {
                $this->assertRegExp(sprintf('!%s!', implode("|", $files)), $msg);

                return;
            });

        $service = \Stego\service();
        $service->setConfiguration(new TestConfiguration());
        $service->getContainer()->set('console:stdio', $mockedConsole);

        $task = $service->getContainer()->get('task:clean');
        $task->run($files);

        foreach ($files as $file) {
            $this->assertTrue(!file_exists($file));
        }
    }
}
