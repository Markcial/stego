<?php

namespace Stego\Console\Commands;

use Composer\Package\Link;
use Stego\Packages\Compiler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Loader extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('loader')
            ->setDescription('Creates the autoloader.')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        $pharFile = 'deps' . DIRECTORY_SEPARATOR . 'stego.phar';

        if (file_exists($pharFile)) {
            @unlink($pharFile);
        }

        $phar = new \Phar($pharFile, 0, basename($pharFile));
        $phar->setSignatureAlgorithm(\Phar::SHA1);
        $phar->startBuffering();

        $phar->addFromString('loader.php', file_get_contents('src/loader.php'));

        $stub = <<<STUB
<?php
Phar::mapPhar('stego.phar');
require 'phar://stego.phar/loader.php';
return new StegoLoader();
__HALT_COMPILER();
?>
STUB;

        $phar->setStub($stub);

        $phar->stopBuffering();

        unset($phar);

    }
} 