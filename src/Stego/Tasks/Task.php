<?php

namespace Stego\Tasks;

use Stego\Console\Stdio\Console;
use Stego\ContainerAware;

trait Task
{
    use ContainerAware;

    protected $required = array();
    /** @var array */
    protected $params = array();
    /** @var Builder */
    protected $builder;
    /** @var Console */
    protected $console;

    /**
     * @param array $params
     *
     * @return int
     */
    public function run($params = array())
    {
        $this->init();
        $this->params = $params;
        $this->parseParams();
        $this->doTask();
    }

    /**
     * @param Builder $builder
     */
    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @return Builder
     */
    public function getBuilder()
    {
        if (is_null($this->builder)) {
            $this->builder = $this->getContainer()->get('builder');
        }

        return $this->builder;
    }

    protected function init()
    {
    }

    abstract protected function doTask();

    /**
     * @param array $keys
     */
    protected function setRequired(array $keys)
    {
        $this->required = $keys;
    }

    /**
     * @return string
     */
    public function getTaskName()
    {
        $class = new \ReflectionClass(get_called_class());

        return preg_replace('!task$!', '', strtolower($class->getShortName()));
    }

    public function getParam($name)
    {
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }

        return false;
    }

    protected function getConsole()
    {
        if (is_null($this->console)) {
            $this->console = $this->getContainer()->get('console:stdio');
        }

        return $this->console;
    }

    public function getParams()
    {
        return $this->params;
    }

    private function parseParams()
    {
        foreach ($this->required as $req) {
            if (!array_key_exists($req, $this->params)) {
                throw new \RuntimeException(sprintf('Missing required parameter : "%s".', $req));
            }
        }

        foreach ($this->params as &$param) {
            $param = $this->getContainer()->parse($param);
        }
    }
}
