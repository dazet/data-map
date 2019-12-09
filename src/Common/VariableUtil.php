<?php declare(strict_types=1);

namespace DataMap\Common;

final class VariableUtil
{
    /** @var callable */
    public const ifEmpty = [self::class, 'ifEmpty'];
    /** @var callable */
    public const ifNull = [self::class, 'ifNull'];

    /**
     * @param mixed $value
     * @param mixed $default
     * @return mixed
     */
    public static function ifEmpty($value, $default)
    {
        return empty($value) ? $default : $value;
    }

    /**
     * @param mixed $value
     * @param mixed $default
     * @return mixed
     */
    public static function ifNull($value, $default)
    {
        return $value ?? $default;
    }
}
