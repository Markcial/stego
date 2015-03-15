<?php

namespace Stego\Console;

use Stego\Service;

class ApplicationTest extends \PHPUnit_Framework_TestCase
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

    public function testApplication()
    {
        $this->markTestSkipped('test pending.');
    }

    public function testGetStdio()
    {
        $mockedStdio = $this->getMockBuilder('\Stego\Console\Commands\Stdio\IOTerm')
            ->disableOriginalConstructor()
            ->getMock();
        \Stego\service()->getDi()->set('console:stdio', $mockedStdio);
        /** @var Application $app */
        $app = \Stego\service()->getApplication();
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
        $mockedStdio = $this->getMockBuilder('\Stego\Console\Commands\Stdio\IOTerm')
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
        \Stego\service()->getDi()->set('console:stdio', $mockedStdio);

        \Stego\service()->getApplication()->shell();
    }
}
