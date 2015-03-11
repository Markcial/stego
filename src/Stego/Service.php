<?php

namespace Stego;

class Service
{
    /** @var Container */
    protected $container;

    protected $version = '0.1b';

    public function __construct()
    {
        $this->container = new Container(array(
            'stego:loader' => '#Stego\Loader',
            'stego:compiler' => '#Stego\Packages\Compiler',
            'stego:inspector' => '#Stego\Packages\Inspector',
            'stego:locator' => '#Stego\Packages\Locator',
            'stego:vars:fs:src' => dirname(__DIR__),
            'stego:vars:fs:root' => dirname(dirname(__DIR__)),
            'stego:vars:deps:metadata' => 'composer.json',
            'stego:vars:deps:folder' => 'deps',
            'stego:vars:deps:pharname' => 'pkg.phar',
            'stego:vars:deps:path' => '%{fs:root}/%{deps:folder}',
            //'stego:vars:deps:schema' => '%{deps:path}/{}',
        ));
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
