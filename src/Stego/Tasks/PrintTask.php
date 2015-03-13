<?php

namespace Stego\Tasks;

use Stego\Console\Commands\Stdio\IOTerm;

class PrintTask
{
    use Task;

    protected function init()
    {
        $this->setRequired(array('message'));
    }

    public function doTask()
    {
        /** @var IOTerm $console */
        $console = $this->getContainer()->get('console:stdio');
        $message = $this->getParam('message');
        $console->write($message);
        return 0;
    }
}
