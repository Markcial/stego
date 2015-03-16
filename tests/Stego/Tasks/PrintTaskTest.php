<?php

namespace Stego\Tasks;

use Stego\Service;
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
        $this->markTestSkipped('check later');
        $service = \Stego\service();
        $service->setConfiguration(new TestConfiguration());

        $builder = $service->getBuilder();
        $mockedTask = $this->getMockBuilder('\Stego\Tasks\PrintTask')->getMock();
        $mockedTask->expects($this->any())->method('setBuilder')->with($builder)->willReturn(null);
        //$mockedTask->expects($this->any())->method('out')->with('%{demo:message}')->willReturn(null);

        $service->getContainer()->set('task:print', $mockedTask);
        $builder->run('test:print');
    }
}
