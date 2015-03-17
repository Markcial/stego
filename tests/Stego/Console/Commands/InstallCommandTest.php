<?php

namespace Stego\Console\Commands;

use Stego\Stubs\TestConfiguration;

class InstallCommandTest extends \PHPUnit_Framework_TestCase
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

    public function testInstallCommand()
    {
        $vendor = 'vendor/project';

        $service = \Stego\service();
        $service->setConfiguration(new TestConfiguration());
        $container = $service->getContainer();

        $command = $container->get('console:commands:install');
        /** @var \PHPUnit_Framework_MockObject_MockObject $mockedManager */
        $mockedManager = $this->getMockBuilder('\Stego\Packages\Manager')->getMock();
        $container->set('manager', $mockedManager);

        $mockedManager->expects($this->once())->method('findPackage')->with($vendor, null)->willReturn(null);
        $mockedManager->expects($this->once())->method('download')->willReturn(null);
        $mockedManager->expects($this->once())->method('extract')->willReturn(null);
        $mockedManager->expects($this->once())->method('toPhar')->willReturn(null);
        $mockedManager->expects($this->once())->method('cleanup')->willReturn(null);

        $command->execute(array($vendor));
    }
}
