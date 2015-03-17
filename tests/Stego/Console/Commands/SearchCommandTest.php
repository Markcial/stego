<?php

namespace Stego\Console\Commands;

use Stego\Stubs\TestConfiguration;

class SearchCommandTest extends \PHPUnit_Framework_TestCase
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

    public function testSearchCommand()
    {
        $vendor = 'vendor/project';
        $version = '';

        $service = \Stego\service();
        $service->setConfiguration(new TestConfiguration());
        $container = $service->getContainer();

        $command = $container->get('console:commands:search');
        /* @var \PHPUnit_Framework_MockObject_MockObject $mockedManager */
        $mockedBrowser = $this->getMockBuilder('\Stego\Packages\Browser')->getMock();
        $container->set('browser', $mockedBrowser);
        $mockedStdio = $this->getMockBuilder('\Stego\Console\Stdio\Console')->getMock();
        $container->set('console:stdio', $mockedStdio);
        $command->setApplication($service->getApplication());

        $data = array(
            'results' => array(
                array(
                    'name' => '',
                    'description' => '',
                    'downloads' => '',
                    'favers' => '',
                ),
            ),
        );

        $mockedBrowser->expects($this->once())->method('find')->with($vendor, null)->willReturn($data);

        $command->execute(array($vendor));
    }
}
