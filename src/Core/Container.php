<?php

namespace Dileep\Mvc\Core;

use ReflectionClass;
use Exception;

class Container
{
    private $bindings = [];
    protected static $instance = null;

    // Your exact bind method!
    public function bind($abstract, $concrete)
    {
        $this->bindings[$abstract] = $concrete;
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function resolve($abstract)
    {
        // 1. MANUAL BINDING CHECK (Your logic)
        // Does a specific recipe exist for this class?
        if (isset($this->bindings[$abstract])) {
            $concrete = $this->bindings[$abstract];
            if (is_callable($concrete)) {
                return $concrete($this);
            }
            return $concrete;
        }

        // 2. AUTO-WIRING FALLBACK (Reflection logic)
        // If no manual binding exists, try to build it dynamically!
        try {
            $reflection = new ReflectionClass($abstract);
        } catch (\ReflectionException $e) {
            throw new Exception("Class {$abstract} does not exist.");
        }

        if (!$reflection->isInstantiable()) {
            throw new Exception("Class {$abstract} is not instantiable (it might be an Interface).");
        }

        $constructor = $reflection->getConstructor();

        // If no constructor, just return a new instance
        if (is_null($constructor)) {
            return new $abstract;
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            // Built-in types or untyped parameters can be satisfied by default values.
            if (!$type || $type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                    continue;
                }

                throw new Exception("Cannot auto-wire built-in types or un-typed parameters for {$parameter->getName()} in {$abstract}");
            }

            // Recursively resolve dependencies
            $dependencies[] = $this->resolve($type->getName());
        }

        return $reflection->newInstanceArgs($dependencies);
    }
}