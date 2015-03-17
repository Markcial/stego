<?php

namespace Stego;

class Configuration
{
    protected $extraDependencies = array();

    public function __construct(array $data = array())
    {
        $this->extraDependencies = $data;
    }

    protected function getBaseDependencies()
    {
        return array(
            // classes
            '!loader'                   => '#Stego\Loader',
            '!compiler'                 => '#Stego\Packages\Compiler',
            '!inspector'                => '#Stego\Packages\Inspector',
            '!locator'                  => '#Stego\Packages\Locator',
            '!browser'                  => '#Stego\Packages\Browser',
            '!manager'                  => '#Stego\Packages\Manager',
            '!downloader'               => '#Stego\Packages\Downloader',
            '!builder'                  => '#Stego\Tasks\Builder',
            '!task:print'               => '#Stego\Tasks\PrintTask',
            '!task:depends'             => '#Stego\Tasks\DependsTask',
            '!task:copy'                => '#Stego\Tasks\Fs\CopyTask',
            '!task:clean'               => '#Stego\Tasks\Fs\CleanTask',
            '!task:phar'                => '#Stego\Tasks\Fs\PharTask',
            '!console:stdio'            => '#Stego\Console\Stdio\Console',
            '!console:application'      => '#Stego\Console\Application',
            '!console:commands:install' => '#Stego\Console\Commands\InstallCommand',
            '!console:commands:loader'  => '#Stego\Console\Commands\LoaderCommand',
            '!console:commands:search'  => '#Stego\Console\Commands\SearchCommand',
            // vars
            '!vars:version' => '0.2b',
            '!vars:fs:root' => getcwd(),
            '!vars:fs:src' => '%{fs:root}/src',
            '!vars:fs:tmp' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'stego',
            '!vars:fs:vendor' => '%{fs:root}/vendor',
            '!vars:zip:tmp' => '%{fs:tmp}/%{dyn:vendor}/%{dyn:version}',
            '!vars:deps:metadata' => '@phar://%{deps:dynamic}/composer.json',
            '!vars:deps:folder' => 'deps',
            '!vars:deps:pharname' => 'package.phar',
            '!vars:deps:path' => '%{fs:root}/%{deps:folder}',
            '!vars:phar:relative' => '%{deps:folder}/%{dyn:vendor}/%{dyn:version}/%{deps:pharname}',
            '!vars:phar:absolute' => '%{deps:path}/%{dyn:vendor}/%{dyn:version}/%{deps:pharname}',
            '!vars:phar:alias' => function () {
                return sprintf(
                    '%s-%s.phar',
                    str_replace('/', '-', $this->get('vars:dyn:vendor')),
                    $this->get('vars:dyn:version')
                );
            },
            '!vars:build:pharname' => 'stego.phar',
            '!vars:build:tmp' => '%{fs:root}/build',
            '!vars:build:dest' => '%{fs:root}/%{deps:folder}/%{build:pharname}',
            // jobs
            '!job:clean' => array(
                'clean' => array('%{build:tmp}/src'),
            ),
            '!job:copy:source' => array(
                'print' => array('message' => '%[comment]Copying source.'),
                'copy' => array('from' => '%{fs:src}', 'to' => '%{build:tmp}/src'),
            ),
            '!job:make:phar' => array(
                'phar' => array(
                    'source' => '%{build:tmp}/src',
                    'destination' => '%{build:dest}',
                    'bootstrap' => 'phar://%{build:pharname}/functions.php',
                ),
            ),
            '!job:build' => array(
                'print' => array('message' => '%[comment]Building file'),
                'depends' => array('clean', 'copy:source', 'copy:deps:guzzle', 'copy:deps:sfevt', 'make:phar'),
            ),
        );
    }

    /**
     * @param $name
     * @param $dependency
     */
    public function addDependency($name, $dependency)
    {
        $this->extraDependencies[$name] = $dependency;
    }

    /**
     * @return array
     */
    protected function getExtraDependencies()
    {
        return $this->extraDependencies;
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return array_merge(
            $this->getBaseDependencies(),
            $this->getExtraDependencies()
        );
    }
}
