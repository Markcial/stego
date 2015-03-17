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

    public function download($vendor, $version)
    {
        $url = $this->getBrowser()->getZipUrl($vendor, $version);
        $temp = $this->getContainer()->get('vars:fs:tmp');
        $this->getDownloader()->download($url, $temp);
    }
}
