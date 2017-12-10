<?php

namespace DataMap\Output;

use DataMap\Common\ObjectInfo;

final class ObjectHydrator implements Formatter
{
    /** @var object */
    private $object;

    /** @var ObjectInfo */
    private $objectInfo;

    public function __construct($object)
    {
        if (\is_object($object)) {
            $this->object = clone $object;
        } elseif (\is_string($object) && \class_exists($object)) {
            $this->object = new $object();
        } else {
            throw new \InvalidArgumentException(sprintf('Expected object or class name got `%s`', \gettype($object)));
        }

        $this->objectInfo = new ObjectInfo($this->object);
    }

    /**
     * @return object
     */
    public function format(array $output)
    {
        $object = clone $this->object;

        foreach ($output as $key => $value) {
            $object = $this->hydrate($object, $key, $value);
        }

        return $object;
    }

    private function hydrate($object, string $key, $value)
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
