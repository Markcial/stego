<?php

namespace Stego\Packages;

use Stego\Console\Commands\Stdio\IOTerm;
use Stego\ContainerAware;

class Downloader
{
    use ContainerAware;

    /** @var IOTerm */
    protected $console;

    public function download(Details $package)
    {
        $temp = $this->getContainer()->get('vars:fs:tmp');
        // first we try the dist details
        $url = $package->getDistUrl();
        $destination = $temp . DIRECTORY_SEPARATOR . basename($url);
        $dir = dirname($destination);

        if (!file_exists($dir)) {
            @mkdir($dir);
        }

        $package->setZipFile($destination);
        $ctx = stream_context_create(
            array(
                "http" => array(
                    "method"  => "GET",
                    "timeout" => 20,
                    "header"  => "User-agent: Stego package manager",
                ),
            ),
            array(
                "notification" => array(&$this, 'notificationCallback'),
            )
        );

        $fp = fopen($url, "r", false, $ctx);
        if (is_resource($fp) && file_put_contents($package->getZipFile(), $fp)) {
            $this->getConsole()->nl();

            return true;
        }

        return false;
    }

    protected function notificationCallback($code, $severity, $message, $messageCode, $downloadedBytes, $totalBytes)
    {
        static $filesize;
        switch ($code) {

            case STREAM_NOTIFY_PROGRESS:
                if ($totalBytes !== 0) {
                    $filesize = $totalBytes;
                }
                if ($downloadedBytes > 0) {
                    if (!isset($filesize)) {
                        $this->getConsole()->write(
                            sprintf("%%[info]\rUnknown filesize.. %2d kb done..", $downloadedBytes/1024),
                            false
                        );
                    } else {
                        $length = (int) (($downloadedBytes/$filesize)*100);
                        $this->getConsole()->write(
                            sprintf(
                                "%%[info]\r[%-100s] %d%% (%2d/%2d kb)",
                                str_repeat("=", $length) . ">",
                                $length,
                                $downloadedBytes/1024,
                                $filesize/1024
                            ),
                            false
                        );
                    }
                }
                break;
        }
    }

    public function getConsole()
    {
        if (is_null($this->console)) {
            $this->console = $this->getContainer()->get('console:stdio');
        }

        return $this->console;
    }
}
