<?php

namespace Stego\Tasks;

use Stego\Builder;
use Stego\Container;
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
        /** @var PrintTask $task */
        $task = \Stego\service()->getDi()->get('task:print');
        assertTrue($task instanceof PrintTask);
        assertTrue($task->getBuilder() instanceof Builder);
        assertTrue($task->getContainer() instanceof Container);
        $task->setParams(array('message' => 'foo'));
        assertTrue($task->getParam('message') === 'foo');
    }
}
