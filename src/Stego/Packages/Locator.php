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

        // maybe the dependency is located in the vendor folder
        $path = $di->get('vars:fs:vendor');
        $libPath = $path . DIRECTORY_SEPARATOR . $vendor;
        if (stream_resolve_include_path($libPath) || file_exists($libPath)) {
            return $libPath;
        }
    }
}
