<?php

namespace Stego\Tasks;

class PrintTask
{
    use Task;

    protected function init()
    {
        $this->setRequired(array('message'));
    }

    public function doTask()
    {
        $this->out($this->getParam('message'));
        return 0;
    }
}
