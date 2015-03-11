<?php

namespace Stego;

use Stego\Stubs\SimpleObject;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function addDependenciesDataProvider()
    {
        $dummy = new \stdClass;
        $dummy->eggs = 'spam';

        return array(
            array('foo', 'bar', 'bar'),
            array(
                'spam',  function () {
                    return 'eggs';
                }, 'eggs',
            ),
            array('bacon', new \stdClass(), new \stdClass()),
            array('cheese', function () {
                $obj = $this->get('bacon');
                $obj->eggs = 'spam';
                return $obj;
            }, $dummy),
        );
    }

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
}
