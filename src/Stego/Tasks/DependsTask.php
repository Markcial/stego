<?php

namespace Stego\Tasks;

class DependsTask
{
    use Task;

    protected function doTask()
    {
        foreach ($this->getParams() as $job) {
            $this->getBuilder()->run($job);
        }
    }
}
