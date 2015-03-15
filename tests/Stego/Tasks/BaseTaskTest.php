<?php

namespace Stego\Tasks;

use Stego\Builder;
use Stego\Console\Commands\Stdio\IOTerm;
use Stego\Container;
use Stego\Service;

class BaseTaskTest extends \PHPUnit_Framework_TestCase
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

    public function testRunBaseTask()
    {
        $task = $this->getMockForTrait('\Stego\Tasks\Task');

        $params = array(
            'foo' => 'bar',
        );

        $task->setContainer(new Container());

        $task->expects($this->any())
            ->method('init')
            ->willReturn(null);
        $task->expects($this->any())
            ->method('setParams')
            ->with($params)
            ->willReturn(null);
        $task->expects($this->any())
            ->method('parseParams')
            ->willReturn(null);
        $task->expects($this->any())
            ->method('doTask')
            ->willReturn(null);

        $task->run($params);
    }

    public function testSetBuilderBaseTask()
    {
        $task = $this->getMockForTrait('\Stego\Tasks\Task');

        $builder = new Builder();

        $container = new Container();
        $container->set('builder', $builder);
        $task->setContainer($container);

        $this->assertSame($builder, $task->getBuilder());

        $task->setBuilder($builder);

        $this->assertSame($builder, $task->getBuilder());
    }

    public function testGetConsoleBaseTask()
    {
        $task = $this->getMockForTrait('\Stego\Tasks\Task');

        $mockedConsole = $this->getMockBuilder('\Stego\Console\Commands\Stdio\IOTerm')
            ->disableOriginalConstructor()
            ->getMock();

        $mockedConsole->expects($this->any())
            ->method('write')
            ->with('message')
            ->willReturnCallback(function ($message) {
                $this->assertEquals('message', $message);

                return;
            });

        $container = \Stego\service()->getDi();
        $container->set('console:stdio', $mockedConsole);
        $task->setContainer($container);

        $task->out('message');
    }

    public function testGetTaskName()
    {
        $task = new PrintTask();
        $this->assertEquals('print', $task->getTaskName());
    }

    public function testParamsOnBaseTask()
    {
        $task = $this->getMockForTrait('\Stego\Tasks\Task');

        $params = array(
            'foo' => 'bar',
        );

        $task->setParams($params);

        $this->assertEquals('bar', $task->getParam('foo'));
        $this->assertFalse($task->getParam('spam'));
        $this->assertSame($params, $task->getParams());
    }
}
