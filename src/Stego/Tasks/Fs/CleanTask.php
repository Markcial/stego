<?php

namespace Stego\Tasks\Fs;

use Stego\Tasks\Task;

class CleanTask
{
    use Task;

    protected function init()
    {
    }

    protected function doTask()
    {
        // cleanup
        $where = $this->getParams();
        if (!is_array($where)) {
            @unlink($where);
        }
        foreach ($where as $location) {
            @unlink($location);
        }
    }
}
