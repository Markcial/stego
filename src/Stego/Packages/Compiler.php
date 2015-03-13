<?php

namespace Stego\Packages;

use Stego\ContainerAware;

class Compiler
{
    use ContainerAware;

    private function createStub($pharName, $bootstrap = false)
    {
        $stub = '<?php' . "\n";
        $stub .= sprintf('Phar::mapPhar("%s");', $pharName) . "\n";
        if ($bootstrap) {
            $stub .= sprintf('require "%s";', $bootstrap) . "\n";
        }
        $stub .= '__HALT_COMPILER();' . "\n";
        $stub .= '?>' . "\n";

        return $stub;
    }

    public function compile($destination, $source, $bootstrap = false, $metadata = false)
    {
        $pharName = basename($destination);

        $dir = dirname($destination);

        if (!file_exists($dir)) {
            trigger_error('Destination folder does not exists, creating it.');
            @mkdir($dir, 0777, true);
        }

        if (file_exists($destination)) {
            trigger_error('Destination file already exists, removing.');
            @unlink($destination);
        }

        $phar = new \Phar($destination, 0, $pharName);
        $phar->setSignatureAlgorithm(\Phar::SHA1);
        $phar->startBuffering();

        $phar->buildFromDirectory(realpath($source));

        $phar->setStub($this->createStub($pharName, $bootstrap));

        if ($metadata) {
            $phar->setMetadata($metadata);
        }

        $phar->stopBuffering();

        unset($phar);
    }
}
