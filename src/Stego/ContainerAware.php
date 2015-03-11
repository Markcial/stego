<?php

namespace Stego;

trait ContainerAware
{
    protected $container;

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }
}
