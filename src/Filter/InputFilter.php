<?php declare(strict_types=1);

namespace DataMap\Filter;

final class InputFilter
{
    private string $key;

    private Filters $filters;

    public function __construct(string $key, Filter ...$filters)
    {
        $this->key = $key;
        $this->filters = new Filters(...$filters);
    }

    public function key(): string
    {
        return $this->key;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function transform($value)
    {
        return $this->filters->transform($value);
    }
}
