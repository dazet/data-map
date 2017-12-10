<?php

namespace DataMap\Common;

final class ObjectInfo
{
    /** @var object */
    private $object;

    /** @var string[] */
    private $publicProperties;

    /** @var string[] */
    private $publicMethods;

    /** @var \ReflectionMethod[] */
    private $publicMethodsReflection;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function publicProperties(): array
    {
        return $this->publicProperties ?? $this->publicProperties = \array_keys(\get_object_vars($this->object));
    }

    public function hasPublicProperty(string $key): bool
    {
        return \in_array($key, $this->publicProperties(), true);
    }

    /**
     * @return string[]
     */
    public function publicMethodsWithoutArguments(): array
    {
        $isGetter = function (\ReflectionMethod $method): bool {
            return $method->getNumberOfParameters() === 0;
        };

        return \array_map($this->methodReflectionToName(), \array_filter($this->publicMethodsReflection(), $isGetter));
    }

    /**
     * @return \ReflectionMethod[]
     */
    public function publicMethodsReflection(): array
    {
        return $this->publicMethodsReflection ?? $this->publicMethodsReflection = (new \ReflectionClass($this->object))
                ->getMethods(\ReflectionMethod::IS_PUBLIC & ~\ReflectionMethod::IS_STATIC);
    }

    /**
     * @return string[]
     */
    public function publicMethods(): array
    {
        return $this->publicMethods ?? $this->publicMethods = array_map(
                $this->methodReflectionToName(),
                $this->publicMethodsReflection()
            );
    }

    public function hasPublicMethod(string $name): bool
    {
        return \in_array(\strtolower($name), $this->publicMethods(), true);
    }

    private function methodReflectionToName(): \Closure
    {
        return function (\ReflectionMethod $method): string {
            return \strtolower($method->getName());
        };
    }
}
