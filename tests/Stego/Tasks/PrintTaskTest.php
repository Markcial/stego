<?php

namespace Stego\Tasks;

use Stego\Stubs\TestConfiguration;

class PrintTaskTest extends \PHPUnit_Framework_TestCase
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

    public function testPrintTask()
    {
        $message = 'yadda!';

        $mockedConsole = $this->getMockBuilder('\Stego\Console\Stdio\Console')
            ->disableOriginalConstructor()
            ->getMock();

        $mockedConsole->expects($this->any())
            ->method('write')
            ->willReturnCallback(function ($msg) use ($message) {
                $this->assertEquals($message, $msg);

                return;
            });

        $service = \Stego\service();
        $service->setConfiguration(new TestConfiguration());
        $service->getContainer()->set('console:stdio', $mockedConsole);

        $task = $service->getContainer()->get('task:print');
        $task->run(array('message' => $message));
    }
}
