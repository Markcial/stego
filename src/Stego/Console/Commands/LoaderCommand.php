<?php

namespace Stego\Console\Commands;

class LoaderCommand
{
    use Command;
/*
    protected function configure()
    {
        $this
            ->setName('loader')
            ->setDescription('Creates the autoloader.')
        ;
    }
*/
    public function execute($args = array())
    {
        // function that fetches the dependencies in the src folder
        $di = $this->getContainer();

        $buildFolder = $di->get('vars:build:tmp');
        $dependencies = $di->get('vars:build:deps');

        // bring stego source files to the build folder


        $pharFile = $di->get('vars:build:dest');

        if (file_exists($pharFile)) {
            @unlink($pharFile);
        }

        $phar = new \Phar($pharFile, 0, basename($pharFile));
        $phar->setSignatureAlgorithm(\Phar::SHA1);
        $phar->startBuffering();

        $phar->buildFromDirectory($di->get('vars:fs:src'));

        $stub = <<<STUB
<?php
Phar::mapPhar('{$di->get('vars:build:pharname')}');
require 'phar://{$di->get('vars:build:pharname')}/functions.php';
__HALT_COMPILER();
?>
STUB;

        $phar->setStub($stub);

        $phar->stopBuffering();

        unset($phar);

    }
} 