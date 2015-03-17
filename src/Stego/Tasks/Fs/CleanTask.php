<?php

namespace Stego\Tasks\Fs;

use Stego\Tasks\Task;

class CleanTask
{
    use Task;

    protected function doTask()
    {
        // cleanup
        $where = $this->getParams();
        if (!is_array($where)) {
            $this->getConsole()->write('%[warning]Cleaning folder : ' . $where);
            @unlink($where);
        }
        foreach ($where as $location) {
            $this->getConsole()->write('%[warning]Cleaning folder : ' . $location);
            @unlink($location);
        }
    }
}
