<?php

namespace Stego;

use Stego\Stubs\SimpleObject;

/**
 * Class ContainerTest
 * @package Stego
 * @covers Container
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testAddDependencies()
    {
        $container = new Container();
        $container->set('foo', 'bar');
        $this->assertEquals($container->get('foo'), 'bar');

        $container->set('spam', function () {
            return $this->get('foo');
        });

        $this->assertEquals($container->get('spam'), 'bar');

        $container->set('bacon', '#Stego\Stubs\ComplexObject');
        $this->assertTrue($container->get('bacon')->getSimple() instanceof SimpleObject);
    }

    public function testCacheOnContainer()
    {
        // via setter
        $object = new SimpleObject();
        $container = new Container();
        $container->set('object', $object);
        $this->assertSame($object, $container->get('object'));
        // via class magic creation
        $container->set('class:object', '#\Stego\Stubs\SimpleObject');
        $object = $container->get('class:object');
        $this->assertSame($object, $container->get('class:object'));
    }

    public function testConfigurationOnConstruct()
    {
        $bar = 'BAR';
        $config = array(
            'test:foo' => function () use ($bar) {
                return $bar;
            },
            'test:spam' => function () {
                return $this->get('vars:set');
            }
        );
        $container = new Container($config);
        $container->set('vars:set', 'bacon');

        $this->assertEquals($bar, $container->get('test:foo'));
        $this->assertEquals('bacon', $container->get('test:spam'));
    }

    public function testProtectedDependencies()
    {
        //prefixed stego: dependencies are protected
        $container = new Container();
        $container->set('stego:foo', 'bar');
        try {
            $container->set('stego:foo', 'eggs');
        } catch (\RuntimeException $exc) {
            $this->assertEquals('Protected property "stego:foo" has already been set.', $exc->getMessage());
        }

        // without the stego: prefix can be overwritten
        $container->set('foo', new \stdClass());
        $container->set('foo', new SimpleObject());

        $this->assertTrue($container->get('foo') instanceof SimpleObject);
    }

    public function testVariableReplacement()
    {
        $container = new Container();
        $container->set('stego:vars:foo', '%{bar}, %{dyn:dummy}%{dyn:excl}');
        $container->set('stego:vars:bar', 'yay');
        $container->set('vars:dyn:dummy', 'it works');
        $container->set('vars:dyn:excl', '!');

        $this->assertEquals($container->get('vars:foo'), 'yay, it works!');
    }
}
