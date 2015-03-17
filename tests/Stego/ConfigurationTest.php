<?php

namespace Stego;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testMergeDependenciesOnConfiguration()
    {
        $config = new Configuration();
        $base = $config->getDependencies();
        $config->addDependency('foo', 'bar');
        $extended = $config->getDependencies();

        $this->assertArrayHasKey('foo', $extended);
        $this->assertEquals($extended['foo'], 'bar');
    }
}
