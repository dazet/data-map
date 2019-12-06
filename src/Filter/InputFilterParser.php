<?php

namespace DataMap\Filter;

use DataMap\Exception\FailedToParseGetter;
use function array_map;
use function array_shift;
use function is_callable;
use function str_getcsv;
use function strpos;
use function strtr;
use function trim;

/**
 * Parse FilterLine from string.
 * Example: `input.key | string | trim | strip_tags`
 */
final class InputFilterParser
{
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

    /** @var array<string, InputFilter> */
    private $parsed = [];

    /**
     * @param array<string, Filter> $filterMap [filter_function_name => Filter, ...]
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

    public function parse(string $getter): InputFilter
    {
        if (isset($this->parsed[$getter])) {
            return $this->parsed[$getter];
        }

        if (strpos($getter, '|') === false) {
            return $this->parsed[$getter] = new InputFilter($getter);
        }

        $chain = array_map('\trim', str_getcsv($getter, '|', '`'));

        // first element should be input key
        $key = (string)array_shift($chain);

        if ($key === '') {
            throw new FailedToParseGetter('Input key is empty');
        }

        // rest should be list of filter definitions

        return $this->parsed[$getter] = new InputFilter($key, ...$this->parseFilters($chain));
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
     * @param array<string, Filter>&Filter[] $map
     */
    private function addFilters(array $map): void
    {
        foreach ($map as $key => $filter) {
            if (!$filter instanceof Filter) {
                throw new FailedToParseGetter('FilterLineParser can contain only Filter instances');
            }

            $key = trim((string)$key);

            if ($key === '') {
                throw new FailedToParseGetter('Filter key should not be empty');
            }

            $this->filterMap[$key] = $filter;
        }
    }

    /**
     * @param mixed[] $args
     */
    private function get(string $key, array $args = []): Filter
    {
        if (isset($this->filterMap[$key])) {
            return $this->filterMap[$key]->withArgs($args);
        }

        if (FilterRegistry::has($key)) {
            $this->filterMap[$key] = FilterRegistry::get($key);

            return $this->filterMap[$key]->withArgs($args);
        }

        if ($this->allowFunction && is_callable($key)) {
            return new Filter($key, $args);
        }

        throw new FailedToParseGetter("Cannot resolve filter function for {$key}");
    }

    /**
     * @param string[] $args
     * @return mixed[]
     */
    private function parseArgs(array $args): array
    {
        return array_map(
            static function (string $arg) {
                return self::ARG_REPLACE[$arg] ?? strtr($arg, self::STR_REPLACE);
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
        return array_map(
            function (string $filterDef) {
                $filterArgs = str_getcsv($filterDef, ' ');
                $filterKey = array_shift($filterArgs);

                return $this->get($filterKey, $this->parseArgs($filterArgs));
            },
            $chain
        );
    }
}
