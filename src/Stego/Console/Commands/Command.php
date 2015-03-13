<?php

namespace Stego\Console\Commands;

use Stego\Console\ApplicationAware;

trait Command
{
    use ApplicationAware;

    abstract public function execute($args = array());
}
