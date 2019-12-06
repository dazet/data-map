<?php

namespace DataMap\Common;

use Countable;
use InvalidArgumentException;
use Traversable;
use function count;
use function is_array;
use function is_iterable;
use function iterator_to_array;

final class ArrayUtil
{
    /** @var callable */
    public const canBeArray = [self::class, 'canBeArray'];
    /** @var callable */
    public const toArrayOrNull = [self::class, 'toArrayOrNull'];
    /** @var callable */
    public const toArray = [self::class, 'toArray'];
    /** @var callable */
    public const isCountable = [self::class, 'isCountable'];
    /** @var callable */
    public const countOrNull = [self::class, 'countOrNull'];

    /**
     * @param mixed $value
     */
    public static function canBeArray($value): bool
    {
        return is_array($value)
            || is_iterable($value);
    }

    /**
     * @param mixed $value
     * @return mixed[]|null
     */
    public static function toArrayOrNull($value): ?array
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value instanceof Traversable) {
            return iterator_to_array($value);
        }

        return null;
    }

    /**
     * @param mixed $value
     * @return mixed[]
     */
    public static function toArray($value): array
    {
        $value = self::toArrayOrNull($value);

        if ($value === null) {
            throw new InvalidArgumentException('Given value cannot be casted to array');
        }

        return $value;
    }

    /**
     * @param mixed $value
     */
    public static function isCountable($value): bool
    {
        return is_array($value) || $value instanceof Countable;
    }

    /**
     * @param mixed $value
     */
    public static function countOrNull($value): ?int
    {
        return self::isCountable($value) ? count($value) : null;
    }
}
