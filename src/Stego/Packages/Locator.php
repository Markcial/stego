<?php

namespace Stego\Packages;

use Stego\ContainerAware;
use Stego\Loader;

class Locator
{
    use ContainerAware;

    public function locate($vendor, $version = 'latest')
    {
        $di = $this->getContainer();
        $di->set('vars:dyn:vendor', $vendor);
        $di->set('vars:dyn:version', $version);
        $path = $di->get('vars:phar:relative');

        if (stream_resolve_include_path($path) || file_exists($path)) {
            return require $path;
        }

        /** @var Loader $loader */
        $loader = $this->getContainer()->get('loader');
        // maybe the dependency is located in the vendor folder
        $path = $di->get('vars:fs:vendor');
        $libPath = $path . DIRECTORY_SEPARATOR . $vendor;
        $composerFile = $libPath . DIRECTORY_SEPARATOR . 'composer.json';
        if (file_exists($composerFile)) {
            $composer = json_decode(file_get_contents($composerFile), true);
            if (array_key_exists('autoload', $composer)) {
                if (array_key_exists('psr-0', $composer['autoload'])) {
                    $paths = $composer['autoload']['psr-0'];
                    foreach ($paths as $ns => &$path) {
                        $path = $libPath . DIRECTORY_SEPARATOR . $path;
                        $loader->addPsr0Path($path);
                    }
                } elseif (array_key_exists('psr-4', $composer['autoload'])) {
                    $paths = $composer['autoload']['psr-4'];
                    foreach ($paths as $ns => &$path) {
                        $path = $libPath . DIRECTORY_SEPARATOR . $path;
                        $loader->addPsr4Path($ns, $path);
                    }
                }
            }
        } else {
            $loader->addPsr0Path($libPath . DIRECTORY_SEPARATOR);
        }
        if (stream_resolve_include_path($libPath) || file_exists($libPath)) {
            return $libPath;
        }
    }
}
