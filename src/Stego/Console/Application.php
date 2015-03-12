<?php

namespace Stego\Console;

use Stego\Console\Commands\Command;
use Stego\Console\Commands\Stdio\IOTerm;
use Stego\ContainerAware;
use Stego\Packages\Compiler;

class Application
{
    use ContainerAware;

    /** @var IOTerm */
    protected $stdio;

    public function __construct()
    {
        $name = <<<BANNER
          .----.-.
         /    ( o \
        '|  __ ` ||
Stego    |||  |||-' atomic package manager for PHP.
BANNER;

        //$version = $this->service->getVersion();


    }

    /**
     * @return IOTerm
     */
    public function getStdio()
    {
        if (is_null($this->stdio)) {
            $this->stdio = $this->getContainer()->get('stego:console:stdio');
        }

        return $this->stdio;
    }

    public function run()
    {
        if (PHP_SAPI !== 'cli') {
            throw new \RuntimeException('The console application can only be called via cli.');
        }
        $argv = $_SERVER['argv'];
        array_shift($argv);
        $command = array_shift($argv);
        $command = $this->getCommand($command);

        $command->execute();

        var_dump($_SERVER['argv']);
    }

    /**
     * @return Compiler
     */
    public function getCompiler()
    {
        return $this->getContainer()->get('stego:compiler');
    }

    /**
     * @param $name
     * @return Command
     */
    protected function getCommand($name) {
        $command = $this->getContainer()->get(sprintf('stego:console:commands:%s', $name));
        $command->setApplication($this);
        return $command;
    }
}
