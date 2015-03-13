<?php

namespace Stego;

class Builder
{
    use ContainerAware;

    protected $tasks;

    public function run($job)
    {
        $jobs = $this->getContainer()->get(sprintf('job:%s', $job));
        foreach ($jobs as $task => $params) {
            $taskClass = $this->getContainer()->get(sprintf('task:%s', $task));
            $taskClass->setBuilder($this);
            $taskClass->run($params);
        }
    }
}
