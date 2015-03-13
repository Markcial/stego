<?php


namespace Stego\Packages;

use Stego\ContainerAware;

class Locator
{
    use ContainerAware;

    public function locate($vendor, $version = 'latest')
    {
        $di = $this->getContainer();
        $di->set('vars:dyn:vendor', $vendor);
        $di->set('vars:dyn:version', $version);
        $path = $di->get('vars:deps:dynamic');

        if (stream_resolve_include_path($path) || file_exists($path)) {
            return $path;
        }

        // maybe the dependency is located in the inside of the phar
        var_dump(__DIR__);die;
    }
}
