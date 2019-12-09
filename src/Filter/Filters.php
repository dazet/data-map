<?php declare(strict_types=1);

namespace DataMap\Filter;

use function array_map;

final class Filters
{
    /** @var Filter[] */
    private $filters;

    public function __construct(Filter ...$filters)
    {
        $this->filters = $filters;
    }

    public static function fromCallable(callable ...$filters):self
    {
        return new self(...self::wrapFilters(...$filters));
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function transform($value)
    {
        $result = $value;
        foreach ($this->filters as $filter) {
            $result = $filter($result);
        }

        return $result;
    }

    public function with(callable $filter): self
    {
        $clone = clone $this;
        $clone->filters[] = Filter::wrap($filter);

        return $clone;
    }

    public function withNullable(callable $filter): self
    {
        $clone = clone $this;
        $clone->filters[] = Filter::nullable($filter);

        return $clone;
    }

    /**
     * @return Filter[]
     */
    private static function wrapFilters(callable ...$filters): array
    {
        return array_map([Filter::class, 'wrap'], $filters);
    }
}
