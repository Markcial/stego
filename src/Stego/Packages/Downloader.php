<?php

namespace Stego\Packages;

use Stego\Console\Commands\Stdio\IOTerm;
use Stego\ContainerAware;

class Downloader
{
    use ContainerAware;

    /** @var IOTerm */
    protected $console;

    public function download($url, $path)
    {
        $filePath = $path . basename($url);
        $fp = fopen ($filePath, 'w+');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, array($this, 'progress'));
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_BUFFERSIZE, 4);

        curl_exec($ch);
        curl_close($ch);

        fclose($fp);

        if (filesize($path) > 0) {
            return true;
        }

        return false;
    }

    public function getConsole()
    {
        if (is_null($this->console)) {
            $this->console = $this->getContainer()->get('console:stdio');
        }

        return $this->console;
    }

    protected function progress($rsc, $dSize, $dld, $uSize, $uld)
    {
        $perc = 0;
        if ($dld && $dSize) {
            $perc = (double)($dld / $dSize);
        }
        $this->getConsole()->out(sprintf("\rProgress : %.2f%%", $perc * 100));
    }
}
