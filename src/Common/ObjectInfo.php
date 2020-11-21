<?php declare(strict_types=1);

namespace DataMap\Common;

use Closure;
use ReflectionClass;
use ReflectionMethod;
use function array_filter;
use function array_keys;
use function array_map;
use function get_object_vars;
use function in_array;
use function strtolower;

final class ObjectInfo
{
    private object $object;

    /** @var string[] */
    private array $publicProperties;

    /** @var string[] */
    private array $publicMethods;

    /** @var ReflectionMethod[] */
    private array $publicMethodsReflection;

    public function __construct(object $object)
    {
        $this->object = $object;
    }

    /**
     * @return string[]
     */
    public function publicProperties(): array
    {
        return $this->publicProperties ?? $this->publicProperties = array_keys(get_object_vars($this->object));
    }

    public function hasPublicProperty(string $key): bool
    {
        return in_array($key, $this->publicProperties(), true);
    }

    /**
     * @return string[]
     */
    public function publicMethodsWithoutArguments(): array
    {
        $isGetter = static function (ReflectionMethod $method): bool {
            return $method->getNumberOfParameters() === 0;
        };

        return array_map($this->methodReflectionToName(), array_filter($this->publicMethodsReflection(), $isGetter));
    }

    /**
     * @return ReflectionMethod[]
     */
    public function publicMethodsReflection(): array
    {
        return $this->publicMethodsReflection
            ?? $this->publicMethodsReflection = (new ReflectionClass($this->object))
                ->getMethods(ReflectionMethod::IS_PUBLIC & ~ReflectionMethod::IS_STATIC);
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
        return in_array(strtolower($name), $this->publicMethods(), true);
    }

    private function methodReflectionToName(): Closure
    {
        return function (ReflectionMethod $method): string {
            return strtolower($method->getName());
        };
    }
}
