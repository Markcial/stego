<?php

namespace Stego;

class Container
{
    /** @var \stdClass */
    protected $di;
    /** @var \SplObjectStorage */
    protected $cache;

    public function __construct($deps = array())
    {
        $this->di = new \stdClass();
        $this->cache = new \SplObjectStorage();

        foreach ($deps as $key => $val) {
            $this->set($key, $val);
        }
    }

    /**
     * @param $name
     * @param $dep
     * @throws \Exception
     */
    public function set($name, $dep)
    {
        if (property_exists($this->di, $name)) {
            throw new \Exception(sprintf('The name %s is already used', $name));
        }

        if (is_callable($dep)) {
            $dep = $this->call($dep);
        }

        if (is_string($dep)) {
            if (preg_match('!^#!', $dep)) {

                $class = substr($dep, 1, strlen($dep));
                if (!class_exists($class, true)) {
                    throw new \Exception(sprintf('Class "%s" not found.', $class));
                }

                $dep = $this->newInstance(new \ReflectionClass($class));

                if (array_key_exists('Stego\ContainerAware', class_uses($dep))) {
                    $dep->setContainer($this);
                }
            } elseif (preg_match('!^@!', $dep)) {
                $dep = file_get_contents($dep);
            } elseif (preg_match_all('!%\{[^}]+\}!m', $dep, $matches)) {
                $dep = preg_replace_callback(
                    '!%\{(?P<var>[^}]+)\}!m',
                    function ($match) {
                        return $this->get(sprintf('stego:vars:%s', $match['var']));
                    },
                    $dep
                );
            }
        }

        $this->di->{$name} = $dep;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        if (property_exists($this->di, $name)) {
            return $this->di->{$name};
        }

        throw new \RuntimeException(
            sprintf('Dependency named %s not found.', $name)
        );
    }

    /**
     * @param \ReflectionClass $class
     *
     * @return bool|mixed|object
     */
    protected function getFromCache(\ReflectionClass $class)
    {
        if ($this->cache->contains($class)) {
            return $this->cache[$class];
        }

        return false;
    }

    /**
     * @param \ReflectionClass $class
     * @throws \Exception
     *
     * @return object
     */
    protected function newInstance(\ReflectionClass $class)
    {
        if ($obj = $this->getFromCache($class)) {
            return $obj;
        }

        if (!$class->hasMethod('__construct')) {
            $obj = $class->newInstanceWithoutConstructor();
            $this->cache[$class] = $obj;

            return $obj;
        }

        $constructor = $class->getConstructor();
        $parameters = $constructor->getParameters();

        $args = array();
        /** @var \ReflectionParameter $param */
        foreach ($parameters as $param) {
            if (is_null($param->getClass())) {
                throw new \Exception('Basic type parameters are not allowed.');
            }
            $args[] = $this->newInstance($param->getClass());
        }
        $obj = $class->newInstanceArgs($args);
        $this->cache[$class] = $obj;

        return $obj;
    }

    /**
     * @param callable $closure
     *
     * @return mixed
     */
    protected function call(\Closure $closure)
    {
        $closure = \Closure::bind($closure, $this, get_called_class());
        return call_user_func($closure);
    }
}
