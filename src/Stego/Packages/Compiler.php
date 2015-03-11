<?php

namespace Stego\Packages;

use Composer\Package\Package;
use Stego\ContainerAware;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;

class Compiler implements CompilerInterface
{
    use ContainerAware;

    const BASE = 'deps';
    const PHAR_NAME = 'package.phar';

    public function getDestinationFolder(Package $pkg)
    {
        return self::BASE . DIRECTORY_SEPARATOR .
            $pkg->getName() . DIRECTORY_SEPARATOR .
            $pkg->getPrettyVersion(). DIRECTORY_SEPARATOR;
    }

    protected function createStub()
    {
        $stub = "<?php\n";
        $stub .= sprintf("Phar::mapPhar('%s');\n", self::PHAR_NAME);
        $stub .= '__HALT_COMPILER();' . "\n";
        $stub .= '?>' . "\n";

        return $stub;
    }

    public function compile(Package $package, $source)
    {
        $dirname = $this->getDestinationFolder($package);
        if (!file_exists($dirname)) {
            @mkdir($dirname, 0777, true);
        }

        $pharLocation = $dirname . self::PHAR_NAME;

        if (file_exists($pharLocation)) {
            @unlink($pharLocation);
        }

        $phar = new \Phar($pharLocation, 0, basename(self::PHAR_NAME));
        $phar->setSignatureAlgorithm(\Phar::SHA1);
        $phar->startBuffering();

        $phar->buildFromDirectory(realpath($source));

        $phar->setStub($this->createStub());

        $phar->stopBuffering();

        unset($phar);
    }
}
