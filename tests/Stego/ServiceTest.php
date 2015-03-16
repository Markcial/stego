<?php

namespace Stego;

use Stego\Console\Application;
use Stego\Console\Commands\InstallCommand;
use Stego\Console\Commands\LoaderCommand;
use Stego\Console\Commands\Stdio\IOTerm;
use Stego\Packages\Compiler;
use Stego\Packages\Inspector;
use Stego\Packages\Locator;
use Stego\Stubs\TestConfiguration;

class ServiceTest extends \PHPUnit_Framework_TestCase
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

    public function testOverrideConfigurationWithSame()
    {
        $baseConfig = new TestConfiguration();
        $service = new Service($baseConfig);
        try {
            $service->setConfiguration($baseConfig);
        } catch (\RuntimeException $e) {
            $this->assertEquals("Configuration hasn't changed.", $e->getMessage());
        }
    }

    public function testRetrieveDependencies()
    {
        $service = service();
        $service->setConfiguration(new TestConfiguration());
        $this->assertTrue($service->getContainer()->get('loader') instanceof Loader);
        $this->assertTrue($service->getContainer()->get('compiler') instanceof Compiler);
        $this->assertTrue($service->getContainer()->get('inspector') instanceof Inspector);
        $this->assertTrue($service->getContainer()->get('locator') instanceof Locator);
        $this->assertTrue($service->getContainer()->get('console:stdio') instanceof IOTerm);
        $this->assertTrue($service->getContainer()->get('console:application') instanceof Application);
        $this->assertTrue($service->getContainer()->get('console:commands:install') instanceof InstallCommand);
        $this->assertTrue($service->getContainer()->get('console:commands:loader') instanceof LoaderCommand);
    }
}
