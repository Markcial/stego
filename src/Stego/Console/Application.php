<?php

namespace Stego\Console;

use Stego\Console\Commands\Install;
use Stego\Console\Commands\Loader;
use Stego\Console\Commands\Search;
use Stego\Packages\Compiler;
use Stego\Packages\CompilerInterface;
use Stego\Service;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    /** @var Service */
    protected $service;

    public function __construct()
    {
        $name = <<<BANNER
          .----.-.
         /    ( o \
        '|  __ ` ||
Stego    |||  |||-' atomic package manager for PHP.
BANNER;

        $service = new Service();
        $version = $service->getVersion();
        parent::__construct($name, $version);
    }

    /**
     * @return Compiler
     */
    public function getCompiler()
    {
        return $this->service->getDi()->get('stego:compiler');
    }

    /**
     * @return array|\Symfony\Component\Console\Command\Command[]
     */
    public function getDefaultCommands()
    {
        return array_merge(
            parent::getDefaultCommands(),
            array(
                new Search(),
                new Install(),
                new Loader(),
            )
        );
    }
}
