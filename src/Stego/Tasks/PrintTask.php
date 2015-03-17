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
        $this->getConsole()->write($this->getParam('message'));

        return 0;
    }
}
