<?php

namespace Stego\Tasks\Fs;

use Stego\Stubs\TestConfiguration;

class CopyTaskTest extends \PHPUnit_Framework_TestCase
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

    public function testCopyLinks()
    {
        $file = tempnam(sys_get_temp_dir(), 'dummy');
        $link = tempnam(sys_get_temp_dir(), 'link');
        unlink($link);

        $to = tempnam(sys_get_temp_dir(), 'destination');
        unlink($to);

        touch($file);

        symlink($file, $link);

        $mockedConsole = $this->getMockBuilder('\Stego\Console\Stdio\Console')
            ->disableOriginalConstructor()
            ->getMock();

        $mockedConsole->expects($this->any())
            ->method('write')
            ->willReturnCallback(function ($msg) {

                return;
            });

        $service = \Stego\service();
        $service->setConfiguration(new TestConfiguration());
        $service->getContainer()->set('console:stdio', $mockedConsole);

        $task = $service->getContainer()->get('task:copy');
        $task->run(array('from' => $link, 'to' => $to));

        $this->assertTrue(is_link($to));
        $this->assertTrue(is_link($link));

        $this->assertEquals(readlink($to), $file);
        $this->assertEquals(readlink($link), $file);
        $this->assertEquals(readlink($to), readlink($link));

        // remove the test stubs
        @unlink($file);
        @unlink($link);
        @unlink($to);
    }
}
