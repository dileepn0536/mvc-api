<?php
declare(strict_types=1);

namespace Dileep\Mvc\Core;

use ReflectionClass;
use Exception;

class Container
{
    protected array $bindings = [];
    protected static ?Container $instance = null;
    protected array $instances = [];
    protected array $resolving = [];

    private function __construct()
    {
        // Private constructor to prevent direct instantiation
    }

    // Your exact bind method!
    public function bind(string $abstract, callable $concrete)
    {
        $this->bindings[$abstract] = $concrete;
    }

    public static function getInstance(): Container
    {
        if (!self::$instance) {
            // Lazy initialization of the container
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __clone()
    {
        throw new Exception("Cloning in singleton pattern is not yet allowed");
    }

    public function __wakeup()
    {
        throw new Exception("Unserializing in singleton pattern is not yet allowed");
    }

    public function resolve(string $abstract)
    {
        if (isset($this->resolving[$abstract])) {
            throw new Exception("Circular dependency detected while resolving {$abstract}");
        }

        $this->resolving[$abstract] = true;

        try {
            if (isset($this->instances[$abstract])) {
                return $this->instances[$abstract];
            }
            if (isset($this->bindings[$abstract])) {
                $concrete = $this->bindings[$abstract];

                if (is_callable($concrete)) {
                    $instance = $concrete($this);
                } elseif (is_string($concrete)) {
                    $instance = $this->resolve($concrete);
                } else {
                    $instance = $concrete;
                }

                return $this->instances[$abstract] = $instance;
            }

            $reflection = new ReflectionClass($abstract);

            if (!$reflection->isInstantiable()) {
                throw new Exception("Class {$abstract} is not instantiable.");
            }

            $constructor = $reflection->getConstructor();

            if (is_null($constructor)) {
                return $this->instances[$abstract] = new $abstract;
            }

            $dependencies = [];

            foreach ($constructor->getParameters() as $parameter) {
                $type = $parameter->getType();
                $typeName = $type?->getName();

                if ($type && $type->allowsNull()) {
                    $dependencies[] = null;
                    continue;
                }

                if (!$typeName) {
                    throw new Exception("Cannot resolve \${$parameter->getName()} in {$abstract}");
                }

                if ($type->isBuiltin()) {
                    if ($parameter->isDefaultValueAvailable()) {
                        $dependencies[] = $parameter->getDefaultValue();
                        continue;
                    }

                    throw new Exception("Cannot resolve parameter '{$parameter->getName()}' in class '{$abstract}'");
                }

                $dependencies[] = $this->resolve($typeName);
            }

            return $this->instances[$abstract] = $reflection->newInstanceArgs($dependencies);

        } finally {
            unset($this->resolving[$abstract]);
        }
    }
}