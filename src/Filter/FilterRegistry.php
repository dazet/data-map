<?php declare(strict_types=1);

namespace DataMap\Filter;

use Dazet\TypeUtil\ArrayUtil;
use Dazet\TypeUtil\BooleanUtil;
use Dazet\TypeUtil\DateUtil;
use Dazet\TypeUtil\JsonUtil;
use Dazet\TypeUtil\NumberUtil;
use Dazet\TypeUtil\StringUtil;
use DataMap\Common\VariableUtil;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;

final class FilterRegistry implements ContainerInterface
{
    /**
     * Built-in filters
     * @var array<string, array{0: callable(mixed):mixed, 1?: array<int, mixed>, 2?: bool }>
     */
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

    private function __construct()
    {
    }

    public static function instance(): self
    {
        static $self;

        return $self ?? $self = new self();
    }

    /** @param string $key */
    public function get($key): Filter
    {
        if (!self::has($key)) {
            throw new InvalidArgumentException("Unknown filter: {$key}");
        }

        /** @phpstan-ignore-next-line */
        return new Filter(...self::DEFAULT[$key]);
    }

    /** @param string $key */
    public function has($key): bool
    {
        return isset(self::DEFAULT[$key]);
    }
}
