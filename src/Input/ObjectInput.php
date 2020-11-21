<?php declare(strict_types=1);

namespace DataMap\Input;

use DataMap\Common\ObjectInfo;
use DataMap\Exception\FailedToWrapInput;
use function array_map;
use function in_array;
use function is_object;
use function strtolower;

final class ObjectInput implements Input
{
    private object $object;

    /** @var string[] */
    private array $getterPrefixes;

    private ObjectInfo $objectInfo;

    /**
     * @param string[] $getterPrefixes
     * @throws FailedToWrapInput
     */
    public function __construct(object $object, array $getterPrefixes = ['', 'get', 'is'])
    {
        $this->object = $object;
        $this->getterPrefixes = $getterPrefixes;
        $this->objectInfo = new ObjectInfo($object);
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if ($this->objectInfo->hasPublicProperty($key)) {
            return $this->object->$key;
        }

        $getter = $this->resolveGetter($key);
        if ($getter !== null) {
            return $this->object->$getter();
        }

        return $default;
    }

    public function has(string $key): bool
    {
        return $this->objectInfo->hasPublicProperty($key) || $this->hasGetter($key);
    }

    private function hasGetter(string $key): bool
    {
        return $this->resolveGetter($key) !== null;
    }

    private function resolveGetter(string $key): ?string
    {
        $publicMethods = $this->objectInfo->publicMethodsWithoutArguments();

        foreach ($this->possibleGetters($key) as $getter) {
            if (in_array($getter, $publicMethods, true)) {
                return $getter;
            }
        }

        return null;
    }

    /**
     * @return string[]
     */
    private function possibleGetters(string $key): array
    {
        return array_map(
            static function (string $prefix) use ($key): string {
                return strtolower($prefix . $key);
            },
            $this->getterPrefixes
        );
    }
}
