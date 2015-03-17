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
function &service()
{
    static $service;

    if (!isset($service)) {
        $service = new Service(new Configuration());
    }

    return $service;
}

/**
 * @param $vendor
 * @param string $version
 */
function import($vendor, $version = 'dev-master')
{
    /* @var Locator $locator */
    static $locator;

    if (!isset($locator)) {
        $locator = service()->getContainer()->get('locator');
    }

    if (!$locator->locate($vendor, $version)) {
        throw new \RuntimeException(sprintf('Library %s not found.', $vendor));
    }
}

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

    $builder->run($name);
}

/**
 * @return string
 */
function version()
{
    return service()->getVersion();
}
