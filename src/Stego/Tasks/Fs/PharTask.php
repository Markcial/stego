<?php

namespace Stego\Tasks\Fs;

use Stego\Tasks\Task;

class PharTask
{
    use Task;

    protected function init()
    {
        $this->setRequired(array('pharname', 'source'));
    }

    protected function doTask()
    {
        $destination = $this->getParam('destination') || getcwd();

    }
}
