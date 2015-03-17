<?php

namespace Stego\Packages;

use Stego\ContainerAware;

class Compiler
{
    use ContainerAware;

    private function createStub($pharName, $autoloader = null)
    {
        $stub = '<?php' . "\n";
        $stub .= sprintf('Phar::mapPhar("%s");', $pharName) . "\n";
        if (!is_null($autoloader)) {
            $stub .= $autoloader;
        }
        $stub .= '__HALT_COMPILER();' . "\n";
        $stub .= '?>' . "\n";

        return $stub;
    }

    public function getPsr0Autoloader($psrPaths)
    {
        $paths = var_export($psrPaths, true);

        return <<<PHP
spl_autoload_register(function (\$class) {
    \$psrPaths = {$paths};
    \$file = preg_replace('#\\\\\\|_(?!.+\\\\\\)#', '/', \$class) . '.php';
    foreach (\$psrPaths as \$path) {
        if (stream_resolve_include_path(\$path . \$file) || file_exists(\$path . \$file)) {
            require \$path . \$file;
        }
    }
});
PHP;
    }

    public function getPsr4Autoloader($prsPaths)
    {
        $paths = var_export($prsPaths, true);

        return <<<PHP
spl_autoload_register(function (\$class) {
    // project-specific namespace prefix (\$prefix)
    // base directory for the namespace prefix (\$path)
    \$psrPaths = {$paths};
    foreach (\$psrPaths as \$prefix => \$path) {
        // does the class use the namespace prefix?
        \$len = strlen(\$prefix);
        if (strncmp(\$prefix, \$class, \$len) !== 0) {
            // no, move to the next registered autoloader
            return;
        }

        // get the relative class name
        \$relative_class = substr(\$class, \$len);

        // replace the namespace prefix with the base directory, replace namespace
        // separators with directory separators in the relative class name, append
        // with .php
        \$file = \$path . str_replace('\\\\', '/', \$relative_class) . '.php';

        // if the file exists, require it
        if (file_exists(\$file)) {
            require \$file;
        }
    }
});
PHP;
    }

    public function compile(Details $package, $destination, $bootstrap = false, $metadata = false)
    {
        $pharAlias = $this->getContainer()->get('vars:phar:alias');

        $dir = dirname($destination);

        if (!file_exists($dir)) {
            //trigger_error('Destination folder does not exists, creating it.');
            @mkdir($dir, 0777, true);
        }

        if (file_exists($destination)) {
            //trigger_error('Destination file already exists, removing.');
            @unlink($destination);
        }

        $phar = new \Phar($destination, 0, $pharAlias);
        $phar->setSignatureAlgorithm(\Phar::SHA1);
        $phar->startBuffering();

        $phar->buildFromDirectory($package->getSourceFolder());

        $phar->setStub($this->createStub($pharAlias, $bootstrap));

        if ($metadata) {
            $phar->setMetadata($metadata);
        }

        $phar->stopBuffering();

        unset($phar);
    }
}
