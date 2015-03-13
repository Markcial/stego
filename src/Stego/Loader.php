<?php

namespace Stego;

/**
 * TODO:
 * - add includes support
 * - add class map support
 * - add bin calls support.
 */
class Loader
{
    /** @var array */
    protected $psr0Paths = array();

    protected $psr4Paths = array();

    /**
     * @param $path
     */
    public function addPsr0Path($path)
    {
        $this->psr0Paths[] = $path;
    }

    public function addPsr4Path($prefix, $path)
    {
        $this->psr4Paths[$prefix] = $path;
    }

    public function bootstrap()
    {
        $psr0Paths = $this->psr0Paths;
        spl_autoload_register(function ($class) use ($psr0Paths) {
            $file = preg_replace('#\\\|_(?!.+\\\)#', '/', $class) . '.php';
            foreach ($psr0Paths as $path) {
                if (stream_resolve_include_path($path . $file) || file_exists($path . $file)) {
                    require $path . $file;
                }
            }
        });

        $psr4Paths = $this->psr4Paths;
        spl_autoload_register(function ($class) use ($psr4Paths) {
            // project-specific namespace prefix ($prefix)
            // base directory for the namespace prefix ($path)
            foreach ($psr4Paths as $prefix => $path) {
                // does the class use the namespace prefix?
                $len = strlen($prefix);
                if (strncmp($prefix, $class, $len) !== 0) {
                    // no, move to the next registered autoloader
                    return;
                }

                // get the relative class name
                $relative_class = substr($class, $len);

                // replace the namespace prefix with the base directory, replace namespace
                // separators with directory separators in the relative class name, append
                // with .php
                $file = $path . str_replace('\\', '/', $relative_class) . '.php';

                // if the file exists, require it
                if (file_exists($file)) {
                    require $file;
                }
            }
        });
    }
}
