<?php

namespace Stego\Tasks;

use Stego\Service;

class PrintTaskTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        if (!function_exists('\Stego\service')) {
            Service::setDefaultConfiguration(getcwd() . '/tests/configuration.php');
            require getcwd() . '/src/functions.php';
        }
        if (!function_exists('assertTrue')) {
            require getcwd() . '/vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';
        }
    }

    public function testPrintTask()
    {
        $mockedTask = $this->getMockBuilder('\Stego\Tasks\PrintTask')->getMock();
        $mockedTask->expects($this->once())->method('run')->willReturn(null);
        \Stego\service()->getDi()->set('task:print', $mockedTask);
        \Stego\task('test:print');
    }
}
