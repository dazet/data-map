<?php

namespace DataMap\Getter;

use DataMap\Exception\FailedToInitializeMapper;

final class GetterMap implements \IteratorAggregate
{
    /**
     * Key => Getter association map.
     * @var callable[] [key => callable getter, ...]
     */
    private $map = [];

    public function __construct(iterable $map)
    {
        foreach ($map as $key => $getter) {
            $this->map[$key] = $this->callableGetter($key, $getter);
        }
    }

    public static function fromIterable(iterable $map): self
    {
        if ($map instanceof self) {
            return $map;
        }

        return new self($map);
    }

    public function getIterator(): \Traversable
    {
        yield from $this->map;
    }

    public function merge(self $other): self
    {
        return new self(\array_merge($this->map, $other->map));
    }

    private function callableGetter(string $key, $getter): callable
    {
        if (\is_string($getter)) {
            return new GetRaw($getter);
        }

        if (\is_callable($getter)) {
            return $getter;
        }

        throw new FailedToInitializeMapper(\sprintf('Invalid getter for key %s', $key));
    }
}
