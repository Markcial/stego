<?php

namespace Stego;

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
    /* @var Loader $loader */
    static $loader;
    /* @var Locator $locator */
    static $locator;

    if (!isset($loader)) {
        $loader = service()->getDi()->get('loader');
    }

    if (!isset($locator)) {
        $locator = service()->getDi()->get('locator');
    }

    if ($location = $locator->locate($vendor, $version)) {
        $metadata = service()->getDi()->get('vars:deps:metadata');
        $metadata = json_decode($metadata, true);
        // fallback psr-0
        $loader->addPsr0Path('phar://' . $location . DIRECTORY_SEPARATOR);
        // psr-0
        if ((
            array_key_exists('autoload', $metadata) &&
            array_key_exists('psr-0', $metadata['autoload'])
        )) {
            foreach ($metadata['autoload']['psr-0'] as $ns => $path) {
                $loader->addPsr0Path('phar://' . $location . DIRECTORY_SEPARATOR . $path);
            }
        }
        // psr-4
        if ((
            array_key_exists('autoload', $metadata) &&
            array_key_exists('psr-4', $metadata['autoload'])
        )) {
            foreach ($metadata['autoload']['psr-4'] as $prefix => $path) {
                $loader->addPsr4Path($prefix, 'phar://' . $location . DIRECTORY_SEPARATOR . $path);
            }
        }

        return $loader->bootstrap();
    }

    return trigger_error(sprintf('Library %s not found.', $vendor));
}

// we use the guzzle http library, so we load it
//import('guzzle/guzzle', '3.9.2.0');
//require_once 'vendor/autoload.php';

function shell()
{
    static $app;

    if (!isset($app)) {
        $app = service()->getApplication();
    }

    $app->shell();
}

function run()
{
    static $app;

    if (!isset($app)) {
        $app = service()->getApplication();
    }

    $app->run();
}

function task($name)
{
    static $builder;

    if (!isset($builder)) {
        $builder = service()->getBuilder();
    }

    return $builder->run($name);
}

/**
 * @return string
 */
function version()
{
    return service()->getVersion();
}
