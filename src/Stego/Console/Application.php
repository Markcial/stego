<?php

namespace Stego\Console;

use Stego\Console\Commands\Install;
use Stego\Console\Commands\Search;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
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
    }

    public function getDefaultCommands()
    {
        return array_merge(
            parent::getDefaultCommands(),
            array(
                new Search(),
                new Install()
            )
        );
    }
}
