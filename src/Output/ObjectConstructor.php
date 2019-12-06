<?php

namespace DataMap\Output;

use Closure;
use InvalidArgumentException;
use ReflectionMethod;
use ReflectionParameter;
use function array_map;
use function class_exists;
use function method_exists;
use function sprintf;

final class ObjectConstructor implements Formatter
{
    private const CONSTRUCTOR = '__construct';

    /** @var Closure */
    private $construct;

    /** @var array<string> */
    private $parameters;

    public function __construct(string $class, string $method = self::CONSTRUCTOR)
    {
        $this->assertClassMethodExists($class, $method);
        $constructor = $this->reflectConstructor($class, $method);

        $this->construct = function (array $parameters) use ($class, $method) {
            return $method === self::CONSTRUCTOR ? new $class(...$parameters) : $class::$method(...$parameters);
        };

        $this->parameters = array_map(
            function (ReflectionParameter $parameter): string {
                return $parameter->getName();
            },
            $constructor->getParameters()
        );
    }

    /**
     * @param array<string, mixed> $output
     */
    public function format(array $output): object
    {
        $parameters = array_map(
            function (string $name) use ($output) {
                return $output[$name] ?? null;
            },
            $this->parameters
        );

        return ($this->construct)($parameters);
    }

    private function assertClassMethodExists(string $class, string $method): void
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Class `%s` does not exists.', $class));
        }

        if (!method_exists($class, $method)) {
            throw new InvalidArgumentException(sprintf('Class `%s` does not have method `%s`.', $class, $method));
        }
    }

    private function reflectConstructor(string $class, string $method): ReflectionMethod
    {
        $constructor = new ReflectionMethod($class, $method);

        if (!$constructor->isPublic()) {
            throw new InvalidArgumentException(sprintf('Class method `%s`::`%s` is not public.', $class, $method));
        }

        if (!$constructor->isConstructor() && !$constructor->isStatic()) {
            throw new InvalidArgumentException(
                sprintf('Class factory method `%s`::`%s` is not valid constructor.', $class, $method)
            );
        }

        return $constructor;
    }
}
