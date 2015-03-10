<?php

namespace Stego\Console;

use Stego\Console\Commands\Install;
use Stego\Console\Commands\Loader;
use Stego\Console\Commands\Search;
use Stego\Packages\Compiler;
use Stego\Packages\CompilerInterface;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    /** @var CompilerInterface */
    protected $compiler;

    public function __construct()
    {
        $name = <<<BANNER
          .----.-.
         /    ( o \
        '|  __ ` ||
Stego    |||  |||-' atomic package manager for PHP.
BANNER;

        $version = '0.1b';
        parent::__construct($name, $version);
        $this->setCompiler(new Compiler());
    }

    /**
     * @param CompilerInterface $compiler
     */
    public function setCompiler(CompilerInterface $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * @return mixed
     */
    public function getCompiler()
    {
        return $this->compiler;
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
