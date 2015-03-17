<?php

namespace Stego\Console;

use Stego\Service;
use Stego\Stubs\TestConfiguration;

class ApplicationTest extends \PHPUnit_Framework_TestCase
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

    public function testGetStdio()
    {
        $service = new Service(new TestConfiguration());
        $mockedStdio = $this->getMockBuilder('\Stego\Console\Stdio\Console')
            ->disableOriginalConstructor()
            ->getMock();
        $service->getContainer()->set('console:stdio', $mockedStdio);
        /** @var Application $app */
        $app = $service->getApplication();
        $this->assertTrue($app instanceof Application);
        $this->assertSame($app->getStdio(), $mockedStdio);
    }

    public function testShellApplication()
    {
        $this->markTestSkipped('test pending.');
        /*$mockedApp = $this->getMockBuilder('\Stego\Console\Application')
            ->setMethods(array('shell'))
            ->getMock();*/
        $app = \Stego\service()->getApplication();
        $mockedStdio = $this->getMockBuilder('\Stego\Console\Stdio\Console')
            ->disableOriginalConstructor()
            ->setMethods(array('readline', 'areArgsValid', 'getCommand', 'write'))
            ->getMock();
        $mockedStdio->expects($this->once())->method('readline')->willReturn(null);
        $mockedStdio->expects($this->once())->method('areArgsValid')->willReturn(true);
        $mockedStdio->expects($this->once())->method('getCommand')->willReturn('usage');
        $mockedStdio->expects($this->once())->method('write')->willReturnCallback(function () use ($app) {
            $mustQuit = new \ReflectionProperty($app, 'mustQuit');
            $mustQuit->setAccessible(true);
            $mustQuit->setValue($app, true);

            return;
        });
        /*\Stego\service()->getDi()->set('console:application', $mockedApp);*/
        \Stego\service()->getContainer()->set('console:stdio', $mockedStdio);

        \Stego\service()->getApplication()->shell();
    }

    public function testApplicationRunFunction()
    {
        $command = 'some-command';

        $mockedApp = $this->getMockBuilder('\Stego\Console\Application')
            ->getMock();

        $mockedStdio = $this->getMockBuilder('\Stego\Console\Stdio\Console')
            ->getMock();

        $mockedStdio->expects($this->any())->method('getCommand')->willReturn($command);

        $mockedApp->expects($this->any())->method('getStdio')->willReturn($mockedStdio);
        $mockedApp->expects($this->any())->method('runCommand')->with($command)->willReturn(null);

        $mockedApp->run();
    }
}
