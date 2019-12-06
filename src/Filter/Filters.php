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

    public function with(callable ...$filters): self
    {
        return new self(...$this->filters, ...self::wrapFilters(...$filters));
    }

    public function withNullable(callable ...$filters): self
    {
        return new self(...$this->filters, ...self::wrapNullableFilters(...$filters));
    }

    public function merge(Filters $other): self
    {
        return new self(...$this->filters, ...$other->filters);
    }

    /**
     * @return Filter[]
     */
    private static function wrapFilters(callable ...$filters): array
    {
        return array_map([Filter::class, 'wrap'], $filters);
    }

    /**
     * @return Filter[]
     */
    private static function wrapNullableFilters(callable ...$filters): array
    {
        return array_map([Filter::class, 'nullable'], $filters);
    }
}
