<?php

namespace DataMap\Common;

final class StringUtil
{
    private function __construct()
    {
    }

    public static function canBeString($value): bool
    {
        return \is_scalar($value) || (\is_object($value) && \method_exists($value, '__toString'));
    }

    public static function toStringOrNull($value): ?string
    {
        if ($value === null || !self::canBeString($value)) {
            return null;
        }

        if (\is_bool($value)) {
            return $value ? '1' : '0';
        }

        return (string)$value;
    }

    public static function toString($value): string
    {
        $string = self::toStringOrNull($value);

        if ($string === null) {
            throw new \InvalidArgumentException('Given value cannot be casted to string');
        }

        return $string;
    }
}
