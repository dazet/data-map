<?php

namespace DataMap\Filter;

use DataMap\Exception\FailedToParseGetter;

/**
 * Parse FilterChain from string.
 * Example: `input.key | string | trim | strip_tags`
 */
final class FilterChainParser
{
    /** @var callable[][] */
    public const DEFAULT_FILTERS = [
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
    public const ARG_REPLACE = [':null' => null, ':false' => false, ':true' => true, ':[]' => []];
    public const STR_REPLACE = ['\\|' => '|'];

    /** @var Filter[] array<string, Filter> [filter_function_name => Filter, ...] */
    private $filterMap;

    /**
     * Allow any PHP function (or other callable passed as string) when filter function name is not defined.
     * In safe mode you will not be able to use `key | strval | trim` unless you strictly define these filter functions.
     * @var bool
     */
    private $allowFunction;

    /** @var FilterChain[] */
    private $parsed = [];

    /**
     * @param Filter[] $filterMap array<string, Filter> [filter_function_name => Filter, ...]
     */
    public function __construct(array $filterMap, bool $allowFunction = true)
    {
        $this->filterMap = [];
        $this->allowFunction = $allowFunction;
        $this->addFilters($filterMap);
    }

    public static function default(): self
    {
        static $default;

        return $default ?? $default = new self([]);
    }

    public static function safeDefault(): self
    {
        static $default;

        return $default ?? $default = new self([], false);
    }

    public function parse(string $getter): FilterChain
    {
        if (isset($this->parsed[$getter])) {
            return $this->parsed[$getter];
        }

        if (\strpos($getter, '|') === false) {
            return $this->parsed[$getter] = new FilterChain($getter);
        }

        // split by `|` except escaped `\|`
        $chain = \preg_split('/[^\\\\]\|/', $getter);

        if ($chain === false) {
            throw new FailedToParseGetter('Failed to split transformation filters');
        }

        $chain = \array_map('\trim', $chain);

        // first element should be input key
        $key = (string)\array_shift($chain);

        if ($key === '') {
            throw new FailedToParseGetter('Input key is empty');
        }

        // rest should be list of filter definitions

        return $this->parsed[$getter] = new FilterChain($key, ...$this->parseFilters($chain));
    }

    /**
     * @param Filter[] $filters
     */
    public function withFilters(array $filters): self
    {
        $copy = clone $this;
        $copy->addFilters($filters);

        return $copy;
    }

    public function withFilter(string $key, Filter $filter): self
    {
        return $this->withFilters([$key => $filter]);
    }

    /**
     * @param Filter[] $map array<string, Filter>
     */
    private function addFilters(array $map): void
    {
        foreach ($map as $key => $filter) {
            if (!$filter instanceof Filter) {
                throw new FailedToParseGetter('FilterChainParser can contain only Filter instances');
            }

            $key = \trim((string)$key);

            if ($key === '') {
                throw new FailedToParseGetter('Filter key should not be empty');
            }

            $this->filterMap[$key] = $filter;
        }
    }

    private function get(string $key, array $args = []): Filter
    {
        if (isset($this->filterMap[$key])) {
            return $this->filterMap[$key]->withArgs($args);
        }

        if (isset(self::DEFAULT_FILTERS[$key])) {
            $this->filterMap[$key] = new Filter(...self::DEFAULT_FILTERS[$key]);

            return $this->filterMap[$key]->withArgs($args);
        }

        if ($this->allowFunction && \is_callable($key)) {
            return new Filter($key, $args);
        }

        throw new FailedToParseGetter("Cannot resolve filter function for {$key}");
    }

    private function parseArgs(array $args): array
    {
        return \array_map(
            function (string $arg) {
                return self::ARG_REPLACE[$arg] ?? \strtr($arg, self::STR_REPLACE);
            },
            $args
        );
    }

    /**
     * @param string[] $chain
     * @return Filter[]
     * @throws FailedToParseGetter
     */
    private function parseFilters(array $chain): array
    {
        return \array_map(
            function (string $filterDef) {
                $filterArgs = \str_getcsv($filterDef, ' ');
                $filterKey = \array_shift($filterArgs);

                return $this->get($filterKey, $this->parseArgs($filterArgs));
            },
            $chain
        );
    }
}
