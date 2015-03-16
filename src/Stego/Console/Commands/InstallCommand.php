<?php

namespace Stego\Console\Commands;

class InstallCommand
{
    use Command;

    protected function install($package)
    {
        $tmp = $this->getContainer()->get('vars:fs:tmp');
        /* @var Package $package */
        $this->getComposer()->getDownloadManager()->download($package, $tmp);

        $this->getContainer()->get('compiler')->compile($package, $tmp);

        @unlink($tmp);

        return $package;
    }

    protected function downloadLibrary($vendor, $version)
    {
        $container = $this->getContainer();
        $tmp = $container->get('vars:fs:tmp');
        $browser = $container->get('browser');

        $zipUrl = $browser->getZipUrl($vendor, $version);
    }

    public function execute($args = array())
    {
        $library = array_shift($args);
        $version = array_shift($args);

        $this->downloadLibrary($library, $version);
    }
}
