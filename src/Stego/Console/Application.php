<?php

namespace Stego\Console;

use Stego\Console\Commands\Command;
use Stego\Console\Commands\Stdio\IOTerm;
use Stego\ContainerAware;

class Application
{
    use ContainerAware;

    /** @var IOTerm */
    protected $stdio;

    protected $mustQuit = false;

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
            $this->stdio = $this->getContainer()->get('console:stdio');
        }

        return $this->stdio;
    }

    public function shell()
    {
        if (PHP_SAPI !== 'cli') {
            throw new \RuntimeException('The console application can only be called via cli.');
        }

        while (!$this->mustQuit) {
            $this->getStdio()->readline();
            if (!$this->getStdio()->areArgsValid()) {
                $command = 'usage';
                $this->runCommand($command);
                continue;
            }

            $command = $this->getStdio()->getCommand();
            $this->runCommand($command);
        }
    }

    public function run()
    {
        $command = $this->getStdio()->getCommand();

        return $this->runCommand($command);
    }

    protected function runCommand($name)
    {
        // maybe the command is  asimple call to the application
        if (method_exists($this, $name)) {
            return call_user_func(array($this, $name));
        }
        // or maybe is a dependency that needs to be loaded
        try {
            $command = $this->getCommand($name);
        } catch (MissingDependencyException $exc) { // create exception for not found dependencies
            $this->getStdio()->write('%[error]' . $exc->getMessage());

            return $this->runCommand('usage');
        }

        return $command->execute($this->getStdio()->getArgs());
        //$command->execute($ags);
    }

    public function usage()
    {
        $usage = <<<HELP
usage of the shell.
command [--args]

avaliable commands
    loader
    search
    install
    usage|help
    version
    exit
HELP;

        $this->getStdio()->write('%[info]' . $usage);
    }

    /**
     * @param $name
     *
     * @return Command
     */
    protected function getCommand($name)
    {
        $command = $this->getContainer()->get(sprintf('console:commands:%s', $name));
        $command->setApplication($this);

        return $command;
    }
}
