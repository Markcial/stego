<?php

namespace Stego\Tasks\Fs;

use Stego\Tasks\Task;

class PharTask
{
    use Task;

    protected function init()
    {
        $this->setRequired(array('source', 'destination'));
    }

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

    protected function doTask()
    {
        $source = $this->getParam('source');
        $destination = $this->getParam('destination');
        $bootstrap = $this->getParam('bootstrap');
        $pharName = basename($destination);

        $dir = dirname($destination);

        if (!file_exists($dir)) {
            $this->out('%[comment]Destination folder does not exists, creating it.');
            @mkdir($dir, 0777, true);
        }

        if (file_exists($destination)) {
            $this->out('%[warning]Destination file already exists, removing.');
            @unlink($destination);
        }

        $phar = new \Phar($destination, 0, $pharName);
        $phar->setSignatureAlgorithm(\Phar::SHA1);
        $phar->startBuffering();

        $phar->buildFromDirectory(realpath($source));

        $phar->setStub($this->createStub($pharName, $bootstrap));

        $phar->stopBuffering();

        unset($phar);
    }
}
