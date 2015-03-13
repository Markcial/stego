<?php

namespace Stego\Tasks\Fs;

use Stego\Tasks\Task;

class CopyTask
{
    use Task;

    protected function init()
    {
        $this->setRequired(array('from', 'to'));
    }

    protected function doTask()
    {
        $from = $this->getParam('from');
        $to = $this->getParam('to');
        $this->doCopy($from, $to);
    }

    private function doCopy($from, $to)
    {
        if (is_link($from)) {
            return symlink(readlink($from), $to);
        }

        if (is_file($from)) {
            return copy($from, $to);
        }

        if (!file_exists($to)) {
            @mkdir($to, 0755, true);
        }

        $dir = dir($from);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            $this->doCopy(
                $from . DIRECTORY_SEPARATOR . $entry,
                $to . DIRECTORY_SEPARATOR . $entry
            );
        }
        $dir->close();
        return 0;
    }
}
