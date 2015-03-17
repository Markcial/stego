<?php

namespace Stego;

use Stego\Console\Application;
use Stego\Tasks\Builder;

class Service
{
    /** @var Container */
    protected $container;
    /** @var Configuration */
    protected $configuration;

    protected $version = '0.1b';
    /** @var Builder */
    protected $builder;

    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration = null)
    {
        $this->configuration = $configuration;
        $this->container = new Container($this->configuration);
    }

    /**
     * @param Configuration $configuration
     *
     * @return bool
     */
    public function setConfiguration(Configuration $configuration)
    {
        if ($configuration === $this->configuration) {
            return trigger_error("Configuration hasn't changed.");
        }

        $this->configuration = $configuration;
        $this->container = new Container($this->configuration);
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->getContainer()->get('console:application');
    }

    /**
     * @return mixed|Builder
     */
    public function getBuilder()
    {
        if (is_null($this->builder)) {
            $this->builder = $this->getContainer()->get('builder');
        }

        return $this->builder;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->getContainer()->get('vars:version');
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}
