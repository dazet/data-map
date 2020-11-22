<?php declare(strict_types=1);

namespace DataMap\Filter;

use DataMap\Exception\FailedToParseGetter;
use Psr\Container\ContainerInterface;
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

    /**
     * Private cache of resolved Filter instances
     * @var array<string, Filter> [filter_function_name => Filter, ...]
     */
    private array $filterMap;

    /**
     * Allow any PHP function (or other callable passed as string) when filter function name is not defined.
     * In safe mode you will not be able to use `key | strval | trim` unless you strictly define these filter functions.
     */
    private bool $allowFunction;

    /** @var array<string, InputFilter> */
    private array $parsed = [];

    /** Container of Filter instances */
    private ContainerInterface $registry;

    /**
     * @param array<string, Filter> $filterMap [filter_function_name => Filter, ...]
     */
    public function __construct(array $filterMap, bool $allowFunction = true, ?ContainerInterface $registry = null)
    {
        $this->filterMap = [];
        $this->allowFunction = $allowFunction;
        $this->registry = $registry ?? FilterRegistry::instance();
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
     * @param array<string, Filter> $filters
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
     * @param array<string, Filter> $map
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

        if ($this->registry->has($key)) {
            return $this->filterFromRegistry($key)->withArgs($args);
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

    private function filterFromRegistry(string $key): Filter
    {
        $filter = $this->registry->get($key);
        if (!$filter instanceof Filter) {
            throw new FailedToParseGetter('Filter registry container must return Filter instance');
        }

        return $this->filterMap[$key] = $filter;
    }
}
