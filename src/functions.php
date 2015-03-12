<?php

namespace Stego;

use Stego\Packages\Locator;

spl_autoload_register(function ($class) {
    $file = preg_replace('#\\\|_(?!.+\\\)#', '/', $class) . '.php';
    $path = __DIR__ . DIRECTORY_SEPARATOR . $file;
    if (stream_resolve_include_path($path) || file_exists($path)) {
        require $path;
    }
});

/**
 * @return Service
 */
function service()
{
    static $service;

    if (!isset($service)) {
        $service = new Service();
    }

    return $service;
}

/**
 * @param $vendor
 * @param string $version
 */
function import($vendor, $version = 'latest')
{
    /** @var Loader $loader */
    static $loader;
    /** @var Locator $locator */
    static $locator;

    if (!isset($loader)) {
        $loader = service()->getDi()->get('stego:loader');
    }

    if (!isset($locator)) {
        $locator = service()->getDi()->get('stego:locator');
    }

    if ($location = $locator->locate($vendor, $version)) {
        $metadata = service()->getDi()->get('stego:vars:deps:metadata');
        $metadata = json_decode($metadata, true);
        // psr-0
        if (
            array_key_exists('autoload', $metadata) &&
            array_key_exists('psr-0', $metadata['autoload'])
        ) {
            foreach ($metadata['autoload']['psr-0'] as $ns => $path) {
                $loader->addPsr0Path('phar://' . $location . DIRECTORY_SEPARATOR . $path);
            }
        }
        // psr-4
        if (
            array_key_exists('autoload', $metadata) &&
            array_key_exists('psr-4', $metadata['autoload'])
        ) {
            foreach ($metadata['autoload']['psr-4'] as $prefix => $path) {
                $loader->addPsr4Path($prefix, 'phar://' . $location . DIRECTORY_SEPARATOR . $path);
            }
        }
        $loader->bootstrap();
    }
}

function shell()
{
    static $app;

    if (!isset($app)) {
        $app = service()->getApplication();
    }

    $keepLoop = true;
    while ($keepLoop) {
        $input = readline("\033[0;34mStg>\033[0m ");

        
        if ($input === 'exit') {
            $keepLoop = false;
        }
    }
}

/**
 * @return string
 */
function version()
{
    return service()->getVersion();
}
