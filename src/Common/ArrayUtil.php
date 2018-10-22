<?php

namespace DataMap\Common;

final class ArrayUtil
{
    private function __construct()
    {
    }

    public static function canBeArray($value): bool
    {
        return \is_array($value)
            || \is_iterable($value);
    }

    public static function toArrayOrNull($value): ?array
    {
        if (\is_array($value)) {
            return $value;
        }

        if ($value instanceof \Traversable) {
            return \iterator_to_array($value);
        }

        if (\is_iterable($value)) {
            $array = [];
            foreach ($value as $k => $v) {
                $array[$k] = $v;
            }

            return $array;
        }

        return null;
    }

    public static function toArray($value): array
    {
        $value = self::toArrayOrNull($value);

        if ($value === null) {
            throw new \InvalidArgumentException('Given value cannot be casted to array');
        }

        return $value;
    }

    public static function isCountable($value): bool
    {
        return \is_array($value) || $value instanceof \Countable;
    }

    public static function countOrNull($value): ?int
    {
        return self::isCountable($value) ? \count($value) : null;
    }
}
