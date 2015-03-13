<?php

namespace Stego;

class Service
{
    /** @var Container */
    protected $container;

    protected $version = '0.1b';

    public function __construct($config = array())
    {
        $this->container = new Container($config);
    }

    public function getApplication()
    {
        return $this->getDi()->get('console:application');
    }

    public function getBuilder()
    {
        return $this->getDi()->get('builder');
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return Container
     */
    public function getDi()
    {
        return $this->container;
    }
}
