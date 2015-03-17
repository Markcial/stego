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

    protected $banner = <<<BANNER
          .----.-.
         /    ( o \
        '|  __ ` ||
Stego    |||  |||-' atomic package manager for PHP.
BANNER;

    protected $mustQuit = false;

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

        $this->getStdio()->write("%[comment]" . $this->banner);
        $this->getStdio()->nl();

        set_error_handler(array(&$this, 'errorHandler'));

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

    protected function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        $this->getStdio()->write('%[error]' . $errstr);
        $this->usage();
    }

    protected function runCommand($name)
    {
        // maybe the command is  asimple call to the application
        if (method_exists($this, $name)) {
            return call_user_func(array($this, $name));
        }
        // or maybe is a dependency that needs to be loaded
        $command = $this->getCommand($name);
        if ($command) {
            $retCode = $command->execute($this->getStdio()->getArgs());
            if ($retCode === 0) {
                $this->getStdio()->write(sprintf("%%[info]Command %s completed succesfully", $name));
            } else {
                $this->getStdio()->write("%[error]command ended with unexpected code.");
            }
        }
    }

    public function version()
    {
        $this->getStdio()->write('%[info]' . $this->getContainer()->get('vars:version'));
    }

    public function usage()
    {
        $usage = <<<HELP
usage of the shell.
command [--args]

avaliable commands
    loader
    search [library]
    install [library] [version constraint]
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
        $command = sprintf('console:commands:%s', $name);
        if (!$this->getContainer()->has($command)) {
            trigger_error(sprintf('Command named "%s" was not found.', $command));

            return false;
        }
        $command = $this->getContainer()->get($command);
        $command->setApplication($this);

        return $command;
    }
}
