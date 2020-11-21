<?php declare(strict_types=1);

namespace DataMap\Getter;

use DataMap\Exception\FailedToInitializeMapper;
use IteratorAggregate;
use Traversable;
use function array_replace;
use function is_callable;
use function is_string;
use function sprintf;

/**
 * @implements IteratorAggregate<string, callable>
 */
final class GetterMap implements IteratorAggregate
{
    /**
     * Key => Getter association map.
     * @var array<string, callable> [key => callable getter, ...]
     */
    private array $map = [];

    /**
     * @param iterable<string, callable|string> $map
     */
    public function __construct(iterable $map)
    {
        foreach ($map as $key => $getter) {
            $this->map[$key] = $this->callableGetter((string)$key, $getter);
        }
    }

    /**
     * @param iterable<string, callable|string> $map
     */
    public static function fromIterable(iterable $map): self
    {
        if ($map instanceof self) {
            return $map;
        }

        return new self($map);
    }

    /**
     * @return Traversable<string, callable>
     */
    public function getIterator(): Traversable
    {
        yield from $this->map;
    }

    public function merge(self $other): self
    {
        return new self(array_replace($this->map, $other->map));
    }

    /**
     * @param callable|string|mixed $getter
     * @throws FailedToInitializeMapper
     */
    private function callableGetter(string $key, $getter): callable
    {
        if (is_string($getter)) {
            return new GetRaw($getter);
        }

        if (is_callable($getter)) {
            return $getter;
        }

        throw new FailedToInitializeMapper(sprintf('Invalid getter for key %s', $key));
    }
}
