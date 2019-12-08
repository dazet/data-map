<?php declare(strict_types=1);

namespace DataMap\Filter;

use DataMap\Common\ArrayUtil;
use DataMap\Common\BooleanUtil;
use DataMap\Common\DateUtil;
use DataMap\Common\JsonUtil;
use DataMap\Common\NumberUtil;
use DataMap\Common\StringUtil;
use DataMap\Common\VariableUtil;
use InvalidArgumentException;

final class FilterRegistry
{
    /** Built-in filters */
    public const DEFAULT = [
        // key => [Filter callback, [Filter constructor args]]
        // types
        'string' => [StringUtil::toStringOrNull],
        'int' => [NumberUtil::toIntOrNull],
        'integer' => [NumberUtil::toIntOrNull],
        'float' => [NumberUtil::toFloatOrNull],
        'bool' => [BooleanUtil::toBoolOrNull],
        'boolean' => [BooleanUtil::toBoolOrNull],
        'array' => [ArrayUtil::toArrayOrNull],
        // strings
        'explode' => ['\explode', [',', '$$']],
        'implode' => ['\implode', [',', '$$']],
        'upper' => ['\mb_strtoupper'],
        'lower' => ['\mb_strtolower'],
        'trim' => ['\trim'],
        'rtrim' => ['\rtrim'],
        'ltrim' => ['\ltrim'],
        'format' => ['\sprintf', ['%s', '$$']],
        'replace' => ['\str_replace', ['', '', '$$']],
        'strip_tags' => ['\strip_tags'],
        // numbers
        'number_format' => ['\number_format', ['$$', 2, '.', '']],
        'round' => ['\round', ['$$', 0]],
        'floor' => ['\floor', ['$$']],
        'ceil' => ['\ceil', ['$$']],
        // dates
        'datetime' => [DateUtil::toDatetimeOrNull],
        'date_format' => [DateUtil::toDateFormatOrNull, ['$$', 'Y-m-d H:i:s']],
        'date_modify' => [DateUtil::dateModifyOrNull, ['$$', '+0 seconds']],
        'timestamp' => [DateUtil::toTimestampOrNull],
        // json
        'json_encode' => [JsonUtil::toJsonOrNull],
        'json_decode' => [JsonUtil::toArrayOrNull],
        // misc
        'count' => [ArrayUtil::countOrNull],
        'if_null' => [VariableUtil::ifNull, ['$$', null], true],
        'if_empty' => [VariableUtil::ifEmpty, ['$$', null], true],
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
