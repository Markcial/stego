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

            }
        }

        $this->di->{$name} = $dep;
    }

    /**
     * @param $dependency
     * @return mixed
     */
    private function applyVars($dependency)
    {
        if (preg_match_all('!%\{[^}]+\}!m', $dependency, $matches)) {
            return preg_replace_callback(
                '!%\{(?P<var>[^}]+)\}!m',
                function ($match) {
                    return $this->get(sprintf('stego:vars:%s', $match['var']));
                },
                $dependency
            );
        }

        return $dependency;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        if (property_exists($this->di, $name)) {
            $dependency = $this->di->{$name};
            if (is_string($dependency)) {
                $dependency = $this->applyVars($dependency);

                // is a file read?
                if (preg_match('!^@!', $dependency)) {
                    return file_get_contents(substr($dependency, 1, strlen($dependency)));
                }
            }

            return $dependency;
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
     * @param $object
     */
    protected function storeToCache(\ReflectionClass $class, $object)
    {
        $traits = $this->getTraits($class);
        if (in_array('Stego\ContainerAware', $traits)) {
            $object->setContainer($this);
        }

        $this->cache[$class] = $object;
    }

    /**
     * @param \ReflectionClass $class
     * @return array
     */
    private function getTraits(\ReflectionClass $class)
    {
        $traits = array();
        foreach ($class->getTraits() as $trait) {
            $traits[] = $trait->getName();
            $traits = array_merge($this->getTraits($trait), $traits);
        }

        return $traits;
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
            $this->storeToCache($class, $obj);

            return $obj;
        }

        $constructor = $class->getConstructor();
        $parameters = $constructor->getParameters();

        $args = array();
        /** @var \ReflectionParameter $param */
        foreach ($parameters as $param) {
            if (is_null($param->getClass())) {
                if (!$param->isOptional()) {
                    throw new \Exception('Basic type parameters are not allowed.');
                } else {
                    continue;
                }
            }

            $args[] = $this->newInstance($param->getClass());
        }
        $obj = $class->newInstanceArgs($args);
        $this->storeToCache($class, $obj);

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
