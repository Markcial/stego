<?php


namespace Stego\Packages;

use Stego\ContainerAware;

class Locator
{
    use ContainerAware;

    public function locate($vendor, $version = 'latest')
    {
        $di = $this->getContainer();
        $di->set('stego:vars:dyn:vendor', $vendor);
        $di->set('stego:vars:dyn:version', $version);
        $path = $di->get('stego:vars:deps:dynamic');

        return stream_resolve_include_path($path);
    }
}
