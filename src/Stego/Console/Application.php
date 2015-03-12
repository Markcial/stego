<?php

namespace Stego\Console;

use Stego\Console\Commands\Command;
use Stego\Console\Commands\Stdio\IOTerm;
use Stego\Packages\Compiler;
use Stego\Service;

class Application
{
    /** @var Service */
    protected $service;
    /** @var IOTerm */
    protected $stdio;
    /** @var Command[] */
    protected $commands;

    public function __construct()
    {
        $name = <<<BANNER
          .----.-.
         /    ( o \
        '|  __ ` ||
Stego    |||  |||-' atomic package manager for PHP.
BANNER;

        $this->service = new Service();
        //$version = $this->service->getVersion();

        foreach ($this->getCommands() as $command) {
            $command->setApplication($this);
        }
    }

    /**
     * @return IOTerm
     */
    public function getStdio()
    {
        if (is_null($this->stdio)) {
            $this->stdio = $this->service->getDi()->get('stego:console:stdio');
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
        return $this->service->getDi()->get('stego:compiler');
    }

    /**
     * @param $name
     * @return Command
     */
    protected function getCommand($name) {
        if (!array_key_exists($name, $this->commands)) {
            throw new \RuntimeException(sprintf('Command named %s not found', $name));
        }

        return $this->commands[$name];
    }

    /**
     * @return array|Command[]
     */
    public function getCommands()
    {
        if (is_null($this->commands)) {
            $di = $this->service->getDi();
            $this->commands = array(
//                $di->get('stego:commands:install'),
                'loader' => $di->get('stego:console:commands:loader'),
//                $di->get('stego:commands:search'),
            );
        }

        return $this->commands;
    }
}
