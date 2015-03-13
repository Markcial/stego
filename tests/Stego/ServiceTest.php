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
    /**
     * @covers Service::__construct
     */
    public function testConfigurationOnService()
    {
        $this->markTestSkipped('test pending');
    }

    public function testRetrieveDependencies()
    {
        $config = require 'src/configuration.php';
        $service = new Service($config);
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
