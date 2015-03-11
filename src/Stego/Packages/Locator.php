<?php


namespace Stego\Packages;

use Stego\ContainerAware;

class Locator
{
    use ContainerAware;

    public function locate($vendor, $version = 'latest')
    {
        $root = $this->getContainer()->get('stego:vars:fs:root');

    }
}
