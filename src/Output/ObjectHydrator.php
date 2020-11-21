<?php declare(strict_types=1);

namespace DataMap\Output;

use DataMap\Common\ObjectInfo;
use InvalidArgumentException;
use function class_exists;
use function gettype;
use function is_object;
use function is_string;
use function ucfirst;

final class ObjectHydrator implements Formatter
{
    private object $object;

    private ObjectInfo $objectInfo;

    /**
     * @param object|class-string $object Object to hydrate or class that can be constructed without parameters
     */
    public function __construct($object)
    {
        if (is_object($object)) {
            $this->object = clone $object;
        } elseif (is_string($object) && class_exists($object)) {
            $this->object = new $object();
        } else {
            throw new InvalidArgumentException(sprintf('Expected object or class name got `%s`', gettype($object)));
        }

        $this->objectInfo = new ObjectInfo($this->object);
    }

    /**
     * @param array<string, mixed> $output
     */
    public function format(array $output): object
    {
        $object = clone $this->object;

        foreach ($output as $key => $value) {
            $object = $this->hydrate($object, $key, $value);
        }

        return $object;
    }

    /**
     * @param mixed $value
     */
    private function hydrate(object $object, string $key, $value): object
    {
        if ($this->objectInfo->hasPublicProperty($key)) {
            $object->$key = $value;

            return $object;
        }

        $setter = 'set' . ucfirst($key);
        if ($this->objectInfo->hasPublicMethod($setter)) {
            $object->$setter($value);

            return $object;
        }

        $immutableSetter = 'with' . ucfirst($key);
        if ($this->objectInfo->hasPublicMethod($immutableSetter)) {
            return $object->$immutableSetter($value);
        }

        return $object;
    }
}
