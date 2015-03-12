<?php

namespace Stego\Console\Commands;

class Loader
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
        $di = $this->getContainer();

        $pharFile = $di->get('stego:vars:build:dest');

        if (file_exists($pharFile)) {
            @unlink($pharFile);
        }

        $phar = new \Phar($pharFile, 0, basename($pharFile));
        $phar->setSignatureAlgorithm(\Phar::SHA1);
        $phar->startBuffering();

        $phar->buildFromDirectory($di->get('stego:vars:fs:src'));

        $stub = <<<STUB
<?php
Phar::mapPhar('{$di->get('stego:vars:build:pharname')}');
require 'phar://{$di->get('stego:vars:build:pharname')}/functions.php';
__HALT_COMPILER();
?>
STUB;

        $phar->setStub($stub);

        $phar->stopBuffering();

        unset($phar);

    }
} 