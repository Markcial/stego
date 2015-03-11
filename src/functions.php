<?php

namespace Stego;

spl_autoload_register(function ($class) {
    $file = preg_replace('#\\\|_(?!.+\\\)#', '/', $class) . '.php';
    $path = __DIR__ . DIRECTORY_SEPARATOR . $file;
    if (stream_resolve_include_path($path)) {
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
    static $loader;
    static $locator;

    if (!isset($loader)) {
        $loader = service()->getDi()->get('stego:loader');
    }

    if (!isset($locator)) {
        $locator = service()->getDi()->get('stego:locator');
    }

    $location = $locator->locate($vendor, $version);

    var_dump($location);
}

/**
 * @return string
 */
function version()
{
    return service()->getVersion();
}
