<?php

namespace Stego\Tasks;

use Stego\Container;
use Stego\Stubs\TestConfiguration;

class BaseTaskTest extends \PHPUnit_Framework_TestCase
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

    public function testRequiredParams()
    {
        $task = $this->getMockForTrait('\Stego\Tasks\Task');
        $required = new \ReflectionProperty($task, 'required');
        $required->setAccessible(true);
        $required->setValue($task, array('foo'));
        try {
            $task->run();
        } catch (\Exception $e) {
            $this->assertEquals('Missing required parameter : "foo".', $e->getMessage());
        }
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
        $this->markTestSkipped('Console is private here, revisit');
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

        $service = \Stego\service();
        $service->setConfiguration(new TestConfiguration());
        $container = $service->getContainer();
        $container->set('console:stdio', $mockedConsole);
        $task->setContainer($container);
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
