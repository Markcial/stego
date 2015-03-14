<?php

namespace Stego;

class Service
{
    /** @var Container */
    protected $container;

    protected static $defaultConfiguration;

    protected $version = '0.1b';

    public function __construct($config = array())
    {
        if (empty($config)) {
            $config = $this->loadDefaultConfiguration();
        }

        $this->container = new Container($config);
    }

    public static function setDefaultConfiguration($path)
    {
        self::$defaultConfiguration = $path;
    }

    private function loadDefaultConfiguration()
    {
        $cfg = self::$defaultConfiguration;
        if (!stream_resolve_include_path($cfg) || !file_exists($cfg)) {
            throw new \RuntimeException(sprintf('Configuration file "%s" not found', $cfg));
        }

        return require $cfg;
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
