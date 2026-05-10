<?php

namespace Dileep\Mvc\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Dileep\Mvc\Core\Container;

class ContainerTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        // Reset singleton for each test
        $reflection = new \ReflectionClass(Container::class);
        $instance   = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, null);

        $this->container = Container::getInstance();
    }

    public function test_singleton_returns_same_instance(): void
    {
        $instance1 = Container::getInstance();
        $instance2 = Container::getInstance();

        $this->assertSame($instance1, $instance2);
    }

    public function test_bind_and_resolve_works(): void
    {
        $this->container->bind('foo', function() {
            return 'bar';
        });

        $result = $this->container->resolve('foo');
        $this->assertEquals('bar', $result);
    }

    public function test_resolved_instance_is_cached(): void
    {
        $this->container->bind('counter', function() {
            return new \stdClass();
        });

        $instance1 = $this->container->resolve('counter');
        $instance2 = $this->container->resolve('counter');

        // Same object returned both times!
        $this->assertSame($instance1, $instance2);
    }

    public function test_auto_resolves_class_with_no_constructor(): void
    {
        $result = $this->container->resolve(\stdClass::class);
        $this->assertInstanceOf(\stdClass::class, $result);
    }

    public function test_circular_dependency_throws_exception(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/Circular dependency/');

        // Bind A to resolve B, B to resolve A
        $this->container->bind('ClassA', function($c) {
            return $c->resolve('ClassB');
        });

        $this->container->bind('ClassB', function($c) {
            return $c->resolve('ClassA');
        });

        $this->container->resolve('ClassA');
    }

    public function test_cloning_throws_exception(): void
    {
        $this->expectException(\Exception::class);
        $clone = clone $this->container;
    }
}