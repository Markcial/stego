<?php

namespace Stego;

use Stego\Console\Application;
use Stego\Console\Commands\InstallCommand;
use Stego\Console\Commands\LoaderCommand;
use Stego\Console\Commands\Stdio\IOTerm;
use Stego\Packages\Compiler;
use Stego\Packages\Inspector;
use Stego\Packages\Locator;

class ServiceTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @runInSeparateProcess
     */
    public function testSetDefaultConfiguration()
    {
        Service::setDefaultConfiguration('demo.php');
        try {
            new Service();
        } catch (\RuntimeException $e) {
            $this->assertEquals('Configuration file "demo.php" not found', $e->getMessage());
        }
    }

    public function testRetrieveDependencies()
    {
        $service = service();
        $this->assertTrue($service->getDi()->get('loader') instanceof Loader);
        $this->assertTrue($service->getDi()->get('compiler') instanceof Compiler);
        $this->assertTrue($service->getDi()->get('inspector') instanceof Inspector);
        $this->assertTrue($service->getDi()->get('locator') instanceof Locator);
        $this->assertTrue($service->getDi()->get('console:stdio') instanceof IOTerm);
        $this->assertTrue($service->getDi()->get('console:application') instanceof Application);
        $this->assertTrue($service->getDi()->get('console:commands:install') instanceof InstallCommand);
        $this->assertTrue($service->getDi()->get('console:commands:loader') instanceof LoaderCommand);
    }
}
