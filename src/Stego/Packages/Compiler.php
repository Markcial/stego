<?php

namespace Stego\Packages;

use Composer\Package\Package;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;

class Compiler implements CompilerInterface{

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

    /**
     * Removes whitespace from a PHP source string while preserving line numbers.
     *
     * @param  string $source A PHP string
     * @return string The PHP string with the whitespace removed
     */
    private function stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }
        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }
        return $output;
    }
} 