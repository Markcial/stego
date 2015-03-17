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
        $container->set('vars:dyn:vendor', $library);
        $container->set('vars:dyn:version', $version);
        /** @var Manager $manager */
        $manager = $container->get('manager');

        $manager->findPackage($library, $version);
        $manager->download();
        $manager->extract();
        $manager->toPhar();
        $manager->cleanup();

        return 0;
    }
}
