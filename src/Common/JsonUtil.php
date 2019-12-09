<?php declare(strict_types=1);

namespace DataMap\Common;

use function is_array;
use function is_string;
use function json_decode;
use function json_encode;

final class JsonUtil
{
    /** @var callable */
    public const toJsonOrNull = [self::class, 'toJsonOrNull'];
    /** @var callable */
    public const toArrayOrNull = [self::class, 'toArrayOrNull'];

    /**
     * @param mixed $value
     */
    public static function toJsonOrNull($value): ?string
    {
        $json = json_encode($value, JSON_UNESCAPED_UNICODE);

        return $json !== false ? $json : null;
    }

    /**
     * @param mixed $value
     * @return mixed[]|null
     */
    public static function toArrayOrNull($value): ?array
    {
        if (!is_string($value)) {
            return null;
        }

        $array = json_decode($value, true);

        return is_array($array) ? $array : null;
    }
}
