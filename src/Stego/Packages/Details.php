<?php

namespace Stego\Packages;

class Details
{
    protected $zipFile;

    protected $packagistData = array();
    protected $sourceFolder;

    public function __construct(array $details)
    {
        foreach ($details as $key => $detail) {
            $this->packagistData[$key] = $detail;
        }
    }

    public function __call($name, $args = array())
    {
        if (preg_match('!^get(?P<property>.*)$!', $name, $matches)) {
            $property = lcfirst($matches['property']);
            if (array_key_exists($property, $this->packagistData)) {
                return $this->packagistData[$property];
            }
        }

        throw new \BadMethodCallException(sprintf('Method "%s" not found.', $name));
    }

    public function setZipFile($path)
    {
        $this->zipFile = $path;
    }

    public function getZipFile()
    {
        return $this->zipFile;
    }

    public function getZipFolderName()
    {
        $dist = $this->getDist();
        $url = $dist['url'];
        preg_match(
            '!^https:\/\/api.github.com\/repos\/(?P<owner>[^/]+)\/(?P<project>[^/]+)\/zipball/(?P<ref>.+)$!',
            $url,
            $matches
        );

        return sprintf('%s-%s-%s', $matches['owner'], $matches['project'], substr($dist['reference'], 0, 7));
    }

    public function setSourceFolder($path)
    {
        $this->sourceFolder = $path;
    }

    public function getSourceFolder()
    {
        if (substr($this->sourceFolder, -1) === '/') {
            return rtrim($this->sourceFolder, '/');
        }

        return $this->sourceFolder;
    }

    public function getDistUrl()
    {
        $dist = $this->getDist();

        return $dist['url'];
    }
}
