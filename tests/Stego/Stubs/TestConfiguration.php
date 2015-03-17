<?php

namespace Stego\Stubs;

use Stego\Configuration;

class TestConfiguration extends Configuration
{
    public function getDependencies()
    {
        return array(
            'loader' => '#Stego\Loader',
            'compiler' => '#Stego\Packages\Compiler',
            'inspector' => '#Stego\Packages\Inspector',
            'locator' => '#Stego\Packages\Locator',
            'builder' => '#Stego\Tasks\Builder',
            'task:print' => '#Stego\Tasks\PrintTask',
            'task:depends' => '#Stego\Tasks\DependsTask',
            'task:copy' => '#Stego\Tasks\Fs\CopyTask',
            'task:clean' => '#Stego\Tasks\Fs\CleanTask',
            'task:phar' => '#Stego\Tasks\Fs\PharTask',
            'job:test:print' => array(
                'print' => array('message' => '%{demo:message}'),
            ),
            'job:copy:source' => array(
                'print' => array('message' => '%[comment]Copying source.'),
                'copy' => array('from' => '%{fs:src}', 'to' => '%{build:tmp}/src'),
            ),
            'job:copy:deps:guzzle' => array(
                'print' => array('message' => '%[comment]Copying guzzle dependencies.'),
                'copy' => array('from' => '%{build:guzzle:src}', 'to' => '%{build:tmp}/src'),
            ),
            'job:copy:deps:sfevt' => array(
                'print' => array('message' => '%[comment]Copying symfony event dispatcher dependencies.'),
                'copy' => array('from' => '%{build:sf:evt:src}', 'to' => '%{build:tmp}/src'),
            ),
            'job:make:phar' => array(
                'phar' => array(
                    'source' => '%{build:tmp}/src',
                    'destination' => '%{build:dest}',
                    'bootstrap' => 'phar://%{build:pharname}/functions.php',
                ),
            ),
            'job:build' => array(
                'print' => array('message' => '%[comment]Building file'),
                'depends' => array('clean', 'copy:source', 'copy:deps:guzzle', 'copy:deps:sfevt', 'make:phar'),
            ),
            'console:stdio' => '#Stego\Console\Commands\Stdio\IOTerm',
            'console:application' => '#Stego\Console\Application',
            'console:commands:install' => '#Stego\Console\Commands\InstallCommand',
            'console:commands:loader' => '#Stego\Console\Commands\LoaderCommand',
//    'console:commands:search' => '#Stego\Console\Commands\SearchCommand',
            'vars:demo:message' => 'Some cool message!',
            'vars:version' => '0.0.1a',
            'vars:fs:root' => getcwd(),
            'vars:fs:src' => '%{fs:root}/src',
            'vars:fs:tmp' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'stego',
            'vars:fs:vendor' => '%{fs:root}/vendor',
            'vars:deps:metadata' => '@phar://%{deps:dynamic}/composer.json',
            'vars:deps:folder' => 'deps',
            'vars:deps:pharname' => 'package.phar',
            'vars:deps:path' => '%{fs:root}/%{deps:folder}',
            'vars:deps:dynamic' => '%{deps:path}/%{dyn:vendor}/%{dyn:version}/%{deps:pharname}',
            'vars:build:pharname' => 'stego.phar',
            'vars:build:tmp' => '%{fs:root}/build',
            'vars:build:dest' => '%{fs:root}/%{deps:folder}/%{build:pharname}',
            'vars:build:guzzle:src' => '%{fs:vendor}/guzzle/guzzle/src',
            'vars:build:sf:evt:src' => '%{fs:vendor}/symfony/event-dispatcher',
        );
    }
}
