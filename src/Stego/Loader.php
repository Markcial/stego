<?php

namespace Stego;

/**
 * TODO:
 * - add psr4 support
 * - add includes support
 * - add class map support
 * - add bin calls support
 */
class Loader
{
    /** @var array */
    protected $psr0Paths = array();

    /**
     * @param $path
     */
    public function addPsr0Path($path)
    {
        $this->psr0Paths[] = $path;
    }

    public function bootstrap()
    {
        $psr0Paths = $this->psr0Paths;
        spl_autoload_register(function ($class) use ($psr0Paths) {
            $file = preg_replace('#\\\|_(?!.+\\\)#', '/', $class) . '.php';
            foreach ($psr0Paths as $path) {
                if (stream_resolve_include_path($path . $file)) {
                    require $path . $file;
                }
            }
        });
    }
}
