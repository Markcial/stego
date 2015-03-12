<?php

namespace Stego;

class Service
{
    /** @var Container */
    protected $container;

    protected $version = '0.1b';

    public function __construct($config = array())
    {
        $config = array_merge(array(
            'stego:loader' => '#Stego\Loader',
            'stego:compiler' => '#Stego\Packages\Compiler',
            'stego:inspector' => '#Stego\Packages\Inspector',
            'stego:locator' => '#Stego\Packages\Locator',
            'stego:console:stdio' => '#Stego\Console\Commands\Stdio\IOTerm',
            'stego:console:application' => '#Stego\Console\Application',
            'stego:console:commands:install' => '#Stego\Console\Commands\InstallCommand',
            'stego:console:commands:loader' => '#Stego\Console\Commands\LoaderCommand',
//            'stego:console:commands:search' => '#Stego\Console\Commands\SearchCommand',
            'stego:vars:fs:root' => getcwd(),
            'stego:vars:fs:src' => '%{fs:root}/src',
            'stego:vars:fs:tmp' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'stego',
            'stego:vars:deps:metadata' => '@phar://%{deps:dynamic}/composer.json',
            'stego:vars:deps:folder' => 'deps',
            'stego:vars:deps:pharname' => 'package.phar',
            'stego:vars:deps:path' => '%{fs:root}/%{deps:folder}',
            'stego:vars:deps:dynamic' => '%{deps:path}/%{dyn:vendor}/%{dyn:version}/%{deps:pharname}',
            'stego:vars:build:pharname' => 'stego.phar',
            'stego:vars:build:tmp' => '%{fs:root}/build',
            'stego:vars:build:dest' => '%{fs:root}/%{deps:folder}/%{build:pharname}',
        ), $config);
        $this->container = new Container($config);
    }

    public function getApplication()
    {
        return $this->getDi()->get('stego:console:application');
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
