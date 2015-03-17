<?php

namespace Stego\Console\Commands;

use Stego\Packages\Manager;

class InstallCommand
{
    use Command;

    public function execute($args = array())
    {
        $library = array_shift($args);
        $version = array_shift($args);

        $container = $this->getContainer();
        /** @var Manager $manager */
        $manager = $container->get('manager');
        $manager->download($library, $version);
    }
}
