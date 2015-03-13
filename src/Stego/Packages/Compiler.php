<?php

namespace Stego\Packages;

use Composer\Package\Package;
use Stego\ContainerAware;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;

class Compiler implements CompilerInterface
{
    use ContainerAware;

    protected function createStub($pharName)
    {
        $stub = "<?php\n";
        $stub .= sprintf("Phar::mapPhar('%s');\n", $pharName);
        $stub .= '__HALT_COMPILER();' . "\n";
        $stub .= '?>' . "\n";

        return $stub;
    }

    public function compile($package, $source)
    {
        $container = $this->getContainer();
        $container->set('vars:dyn:vendor', $package->getName());
        $container->set('vars:dyn:version', $package->getVersion());

        $pharDestination = $container->get('vars:deps:dynamic');
        $dir = dirname($pharDestination);

        if (!file_exists($dir)) {
            @mkdir($dir, 0777, true);
        }

        if (file_exists($pharDestination)) {
            @unlink($pharDestination);
        }

        $phar = new \Phar($pharDestination, 0, basename($pharDestination));
        $phar->setSignatureAlgorithm(\Phar::SHA1);
        $phar->startBuffering();

        $phar->buildFromDirectory(realpath($source));

        $phar->setStub($this->createStub(basename($pharDestination)));

        $phar->stopBuffering();

        unset($phar);
    }
}
