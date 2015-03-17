<?php

namespace Stego\Console\Commands\Stdio;

class Input
{
    const PROMPT = "\033[0;34mStg>\033[0m ";

    /** @var string */
    protected $command;
    /** @var array */
    protected $args;

    public function readline()
    {
        $cmd = readline(self::PROMPT);
        readline_add_history($cmd);
        $pieces = explode(" ", trim($cmd));
        $this->command = array_shift($pieces);
        $this->getArgs();
    }

    public function areArgsValid()
    {
        return $this->args !== false;
    }

    public function getArgs()
    {
        if (is_null($this->args)) {
            $argv = $_SERVER['argv'];
            $this->args = array_slice($argv, 2);
        }

        return $this->args;
    }

    public function getCommand()
    {
        if (is_null($this->command)) {
            $argv = $_SERVER['argv'];
            array_shift($argv);
            $this->command = array_shift($argv);
        }

        return $this->command;
    }
}
