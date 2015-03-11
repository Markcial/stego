<?php

namespace Stego\Stubs;

class ComplexObject
{
    protected $simple;
    protected $object;

    public function __construct(SimpleObject $simple, \stdClass $object)
    {
        $this->simple = $simple;
        $this->object = $object;
    }

    /**
     * @return SimpleObject
     */
    public function getSimple()
    {
        return $this->simple;
    }

    /**
     * @return \stdClass
     */
    public function getObject()
    {
        return $this->object;
    }
}
