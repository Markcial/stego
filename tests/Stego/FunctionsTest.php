<?php

namespace Stego;

use Stego\Stubs\TestConfiguration;
use Stego\Tasks\Builder;

class FunctionsTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @backupGlobals enabled
     */
    public function testCreateService()
    {
        $this->assertTrue(service() instanceof Service);
    }

    public function testTasks()
    {
        global $tasks, $taskName;

        $tasks = array(
            'foo',
            'demo',
            'test',
            'damn',
        );
        $mockTask = array('mocked' => array('message' => 'testing'));
        $mockedTask = \Mockery::mock('Stego\Task\Task');
        $mockedTask->shouldReceive('run')->andReturnNull();
        $mockedTask->shouldReceive('setBuilder')
            ->andReturnUsing(function ($builder) {
                $this->assertTrue($builder instanceof Builder);

                return;
            });

        service()->setConfiguration(new TestConfiguration());
        service()->getContainer()->set('task:mocked', $mockedTask);
        foreach ($tasks as $task) {
            service()->getContainer()->set(sprintf('job:%s', $task), $mockTask);
            $taskName = $task;
            task($taskName);
        }
    }

    public function testRunFunction()
    {
        $service = service();
        $service->setConfiguration(new TestConfiguration());
        $app = $this->getMockBuilder('\Stego\Console\Application')->getMock();
        $app->expects($this->once())->method('run')->willReturn(null);
        service()->getContainer()->set('console:application', $app);
        run();
    }

    public function testShellFunction()
    {
        $app = $this->getMockBuilder('\Stego\Console\Application')->getMock();
        $app->expects($this->once())->method('shell')->willReturn(null);
        service()->getContainer()->set('console:application', $app);
        shell();
    }

    public function testImportFunction()
    {
        $mockedLocator = $this->getMockBuilder('\Stego\Packages\Locator')
            ->setMethods(array('locate'))
            ->getMock();

        $mockedLocator->expects($this->any())
            ->method('locate')
            ->willReturnOnConsecutiveCalls(array(true, false));

        $config = new TestConfiguration();
        service()->setConfiguration($config);
        service()->getContainer()->set('locator', $mockedLocator);
        import('some/library');

        try {
            import('another/library', 'v2.4.2');
        } catch (\Exception $e) {
            $this->assertEquals('Library another/library not found.', $e->getMessage());
        }
    }

    public function testVersionFunction()
    {
        $this->assertEquals(service()->getVersion(), version());
    }

    /**
     * @backupGlobals enabled
     */
    public function testSplAutoloadRegister()
    {
        $this->markTestSkipped('find another way to test the loader...');
        service()->setConfiguration(new TestConfiguration());
        global $currentClass;
        $classes = array(
            '\Stego\Stubs\SimpleObject',
            '\Stego\Stubs\EmptyObject',
            '\Stego\Service',
        );

        function preg_replace($pattern, $rep, $class)
        {
            global $currentClass;
            if (!is_null($currentClass)) {
                assertEquals($pattern, '#\\\\|_(?!.+\\\\)#');
                assertEquals($rep, '/');
                assertEquals($class, $currentClass);
            }

            return \preg_replace($pattern, $rep, $class);
        }

        foreach ($classes as $class) {
            $currentClass = substr($class, 1);
            try {
                new $class();
            } catch (\Exception $e) {
                // is ok, just testing the loader
                $this->assertEquals($e->getMessage(), 'stop that before the invocation of cthuluh. Here it comes.');
            }
        }
    }
}
