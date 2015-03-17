<?php

namespace Stego\Tasks;

use Stego\Stubs\TestConfiguration;

class DependsTaskTest extends \PHPUnit_Framework_TestCase
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

    public function testLoadDependingTask()
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
        $service->getContainer()->set('job:test:print', array(
            'print' => array('message' => $message),
        ));

        $task = $service->getContainer()->get('task:depends');
        //$task = $this->getMockBuilder('\Stego\Tasks\DependsTask')->getMock();
        //$task->expects($this->any())->method('getParams')->willReturn('test:print');
        $task->run(array('test:print'));
    }
}
