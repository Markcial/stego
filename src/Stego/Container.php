<?php

namespace Stego;

class Container
{
    /**
     * TODO add environment replacement variables support
     */

    /** @var \stdClass */
    protected $di;
    /** @var \SplObjectStorage */
    protected $cache;
    /** @var array */
    protected $guards = array();
    /** @var string */
    protected $guardKey = '!';

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
     */
    public function set($name, $dep)
    {
        // wants protection for the variable?
        $wantsProtect = $this->wantsProtect($name);
        $name = $this->cleanParam($name);

        // do we have that property inside but already protected?
        if ($this->isProtected($name)) {
            return trigger_error(sprintf('The property named "%s" is set as read only.', $this->cleanParam($name)));
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
        if ($wantsProtect) {
            $this->guards[$name] = true;
        }
    }

    public function has($name)
    {
        // wildcard ?
        if ($this->usesWildcard($name)) {
            return (bool)$this->getValidKeys($name);
        }

        return property_exists($this->di, $name);
    }

    private function getValidKeys($name)
    {
        $pattern = str_replace('*', '[^:]*', $name);
        $keys = array_keys(get_object_vars($this->di));
        return array_filter($keys, function ($key) use ($pattern) {
            return preg_match(sprintf('!^%s$!', $pattern), $key);
        });
    }

    public function isProtected($name)
    {
        return property_exists($this->di, $name) && array_key_exists($name, $this->guards);
    }

    private function wantsProtect($name)
    {
        return strpos($name, $this->guardKey) === 0;
    }

    private function usesWildcard($name)
    {
        return strpos($name, '*') !== false;
    }

    private function cleanParam($param)
    {
        return $this->wantsProtect($param) ? substr($param, strlen($this->guardKey), strlen($param)) : $param;
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
                    return $this->get(sprintf('vars:%s', $match['var']));
                },
                $dependency
            );
        }

        return $dependency;
    }

    private function search($name)
    {
        $keys = $this->getValidKeys($name);
        // php 5.4 support
        $di = $this->di;
        return array_map(function ($key) use (&$di) {
            return $di->{$key};
        }, $keys);
    }

    // wildcard search
    public function find($name)
    {
        if (!$this->usesWildcard($name)) {
            return trigger_error(sprintf('The parameter "%s" does not contain wilcard for pattern matching.', $name));
        }

        $dependencies = $this->search($name);

        return array_map(array($this, 'warmUpDependency'), $dependencies);
    }

    public function parse($var)
    {
        return $this->applyVars($var);
    }

    private function warmUpDependency($dependency)
    {
        if (is_string($dependency)) {
            $dependency = $this->applyVars($dependency);

            // is a file read?
            if (preg_match('!^@!', $dependency)) {
                return file_get_contents(substr($dependency, 1, strlen($dependency)));
            }
        }

        if (is_callable($dependency)) {
            $dependency = $this->call($dependency);
        }

        return $dependency;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            return trigger_error(sprintf('Dependency/es named "%s" were not found.', $name));
        }

        // wildcard search
        if ($this->usesWildcard($name)) {
            return array_map(array($this, 'warmUpDependency'), $this->find($name));
        }

        return $this->warmUpDependency($this->di->{$name});
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
                    trigger_error('Basic type parameters are not allowed.');
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
