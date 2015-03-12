<?php

namespace Stego\Console\Commands\Stdio;

class Input
{
    public function read()
    {
    }

    public function getArgs()
    {
        return $_SERVER['argv'];
    }
} 