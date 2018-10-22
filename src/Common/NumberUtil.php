<?php

namespace DataMap\Common;

final class NumberUtil
{
    private function __construct()
    {
    }

    public static function canBeNumber($value): bool
    {
        return \is_numeric($value) || \is_bool($value)
            || (StringUtil::canBeString($value) && \is_numeric(StringUtil::toString($value)));
    }

    public static function canBeInt($value): bool
    {
        return \is_int($value)
            || (StringUtil::canBeString($value) && \ctype_digit(\ltrim(StringUtil::toString($value), '-')));
    }

    public static function toIntOrNull($value): ?int
    {
        if ($value === null || !self::canBeNumber($value)) {
            return null;
        }

        if (\is_int($value)) {
            return $value;
        }

        if (\is_float($value)) {
            return (int)$value;
        }

        if (\is_bool($value)) {
            return $value ? 1 : 0;
        }

        return (int)(string)$value;
    }

    public static function toInt($value): int
    {
        $value = self::toIntOrNull($value);

        if ($value === null) {
            throw new \InvalidArgumentException('Given value cannot be casted to string');
        }

        return $value;
    }

    public static function toFloatOrNull($value): ?float
    {
        if ($value === null || !self::canBeNumber($value)) {
            return null;
        }

        if (\is_float($value)) {
            return $value;
        }

        return (float)(string)$value;
    }

    public static function toFloat($value): float
    {
        $value = self::toFloatOrNull($value);

        if ($value === null) {
            throw new \InvalidArgumentException('Given value cannot be casted to string');
        }

        return $value;
    }
}
