<?php

namespace Stego\Console;

use Stego\ContainerAware;

trait ApplicationAware {

    use ContainerAware;

    /** @var Application */
    protected $application;

    /**
     * @param Application $app
     */
    public function setApplication(Application $app)
    {
        $this->application = $app;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->getApplication();
    }

    /**
     * @return Commands\Stdio\IOTerm
     */
    public function getStdio()
    {
        return $this->getApplication()->getStdio();
    }
} 