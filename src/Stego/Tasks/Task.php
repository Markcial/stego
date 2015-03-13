<?php

namespace Stego\Tasks;

use Stego\Builder;
use Stego\ContainerAware;

trait Task
{
    use ContainerAware;

    protected $required = array();

    protected $params;
    /** @var Builder */
    protected $builder;

    /**
     * @param array $params
     *
     * @return int
     */
    public function run($params = array())
    {
        $this->init();
        $this->setParams($params);
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
        return $this->builder;
    }

    abstract protected function init();

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
    protected function getTaskName()
    {
        $class = new \ReflectionClass(get_called_class());
        return preg_replace('!task$!', '', strtolower($class->getShortName()));
    }

    public function setParams(array $params)
    {
        $this->params = $params;
    }

    public function getParam($name)
    {
        if (array_key_exists($name, $this->params)) {
            return $this->getContainer()->parse($this->params[$name]);
        }

        return false;
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
    }
}
