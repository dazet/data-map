<?php declare(strict_types=1);

namespace DataMap\Filter;

use InvalidArgumentException;

final class FilterRegistry
{
    /** Built-in filters */
    public const DEFAULT = [
        // key => [Filter callback, [Filter constructor args]]
        // types
        'string' => ['DataMap\Common\StringUtil::toStringOrNull'],
        'int' => ['DataMap\Common\NumberUtil::toIntOrNull'],
        'integer' => ['DataMap\Common\NumberUtil::toIntOrNull'],
        'float' => ['DataMap\Common\NumberUtil::toFloatOrNull'],
        'bool' => ['DataMap\Common\BooleanUtil::toBoolOrNull'],
        'boolean' => ['DataMap\Common\BooleanUtil::toBoolOrNull'],
        'array' => ['DataMap\Common\ArrayUtil::toArrayOrNull'],
        // strings
        'explode' => ['explode', [',', '$$']],
        'implode' => ['implode', [',', '$$']],
        'upper' => ['mb_strtoupper'],
        'lower' => ['mb_strtolower'],
        'trim' => ['trim'],
        'rtrim' => ['rtrim'],
        'ltrim' => ['ltrim'],
        'format' => ['sprintf', ['%s', '$$']],
        'replace' => ['str_replace', ['', '', '$$']],
        'strip_tags' => ['strip_tags'],
        // numbers
        'number_format' => ['number_format', ['$$', 2, '.', '']],
        'round' => ['round', ['$$', 0]],
        'floor' => ['floor', ['$$']],
        'ceil' => ['ceil', ['$$']],
        // dates
        'datetime' => ['DataMap\Common\DateUtil::toDatetimeOrNull'],
        'date_format' => ['DataMap\Common\DateUtil::toDateFormatOrNull', ['$$', 'Y-m-d H:i:s']],
        'date_modify' => ['DataMap\Common\DateUtil::dateModifyOrNull', ['$$', '+0 seconds']],
        'timestamp' => ['DataMap\Common\DateUtil::toTimestampOrNull'],
        // json
        'json_encode' => ['DataMap\Common\JsonUtil::toJsonOrNull'],
        'json_decode' => ['DataMap\Common\JsonUtil::toArrayOrNull'],
        // misc
        'count' => ['DataMap\Common\ArrayUtil::countOrNull'],
        'if_null' => ['DataMap\Common\VariableUtil::ifNull', ['$$', null], true],
        'if_empty' => ['DataMap\Common\VariableUtil::ifEmpty', ['$$', null], true],
    ];

    public static function get(string $key): Filter
    {
        if (!self::has($key)) {
            throw new InvalidArgumentException("Unknown filter: {$key}");
        }

        return new Filter(...self::DEFAULT[$key]);
    }

    public static function has(string $key): bool
    {
        return isset(self::DEFAULT[$key]);
    }
}
