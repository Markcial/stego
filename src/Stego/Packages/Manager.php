<?php

namespace Stego\Packages;

use Stego\ContainerAware;

class Manager
{
    use ContainerAware;
    /** @var Downloader */
    protected $downloader;
    /** @var Browser */
    protected $browser;
    /** @var Details */
    protected $package;
    /** @var Compiler */
    protected $compiler;

    /**
     * @return Downloader
     */
    public function getDownloader()
    {
        if (is_null($this->downloader)) {
            $this->downloader = $this->getContainer()->get('downloader');
        }

        return $this->downloader;
    }

    /**
     * @return Browser
     */
    public function getBrowser()
    {
        if (is_null($this->browser)) {
            $this->browser = $this->getContainer()->get('browser');
        }

        return $this->browser;
    }

    public function getCompiler()
    {
        if (is_null($this->compiler)) {
            $this->compiler = $this->getContainer()->get('compiler');
        }

        return $this->compiler;
    }

    public function findPackage($vendor, $version = null)
    {
        // if the package is not found error
        $details = $this->getBrowser()->versionDetails($vendor, $version);

        $this->package = new Details($details);

        return true;
    }

    public function getPackage()
    {
        return $this->package;
    }

    public function download()
    {
        if (is_null($this->package)) {
            //error
        }

        return $this->getDownloader()->download($this->package);
    }

    public function cleanUp()
    {
        @unlink($this->getContainer()->get('vars:fs:tmp'));
    }

    public function extract()
    {
        $zip = new \ZipArchive();
        if ($zip->open($this->package->getZipFile()) === true) {
            $extractTo = $this->getContainer()->get('vars:zip:tmp');
            $zip->extractTo($extractTo);
            if (substr($extractTo, -1) !== DIRECTORY_SEPARATOR) {
                $extractTo .= DIRECTORY_SEPARATOR;
            }
            $this->package->setSourceFolder($extractTo . $this->package->getZipFolderName());

            return $zip->close();
        }

        return false;
    }

    public function toPhar()
    {
        $container = $this->getContainer();
        $container->set('vars:dyn:vendor', $this->package->getName());
        $container->set('vars:dyn:version', $this->package->getVersion());
        $pharLocation = $container->get('vars:phar:absolute');
        $compiler = $this->getCompiler();
        $composerFile = $this->package->getSourceFolder() . DIRECTORY_SEPARATOR . 'composer.json';
        if (file_exists($composerFile)) {
            $metadata = file_get_contents($composerFile);
            $metadata = json_decode($metadata, true);

            $bootstrap = null;
            if (array_key_exists('autoload', $metadata)) {
                if (array_key_exists('target-dir', $metadata)) {
                    // psr4 legacy style
                    $psrPaths = $metadata['autoload']['psr-0'];
                    foreach ($psrPaths as $ns => &$path) {
                        $path = sprintf('phar://%s/%s', $container->get('vars:phar:alias'), $path);
                    }
                    $bootstrap = $compiler->getPsr4Autoloader($psrPaths);
                } elseif (array_key_exists('psr-0', $metadata['autoload'])) {
                    $psr0Paths = $metadata['autoload']['psr-0'];
                    foreach ($psr0Paths as $ns => &$path) {
                        $path = sprintf('phar://%s/%s', $container->get('vars:phar:alias'), $path);
                    }
                    $bootstrap = $compiler->getPsr0Autoloader($psr0Paths);
                } elseif (array_key_exists('psr-4', $metadata['autoload'])) {
                    $psrPaths = $metadata['autoload']['psr-4'];
                    foreach ($psrPaths as $ns => &$path) {
                        $path = sprintf('phar://%s/%s', $container->get('vars:phar:alias'), $path);
                    }
                    $bootstrap = $compiler->getPsr4Autoloader($psrPaths);
                }
            }
        } else {
            $bootstrap = $compiler->getPsr0Autoloader(array(
                sprintf('phar://%s/', $container->get('vars:phar:alias')),
            ));
        }

        $compiler->compile($this->package, $pharLocation, $bootstrap);
    }
}
