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
        $this->fail('pending testing');
    }

    public function testRetrieveDependencies()
    {
        $service = new Service();
        $this->assertTrue($service->getDi()->get('stego:loader') instanceof Loader);
        $this->assertTrue($service->getDi()->get('stego:compiler') instanceof Compiler);
        $this->assertTrue($service->getDi()->get('stego:inspector') instanceof Inspector);
        $this->assertTrue($service->getDi()->get('stego:locator') instanceof Locator);
        $this->assertTrue($service->getDi()->get('stego:console:stdio') instanceof IOTerm);
        $this->assertTrue($service->getDi()->get('stego:console:application') instanceof Application);
        $this->assertTrue($service->getDi()->get('stego:console:commands:install') instanceof InstallCommand);
        $this->assertTrue($service->getDi()->get('stego:console:commands:loader') instanceof LoaderCommand);
    }
}
