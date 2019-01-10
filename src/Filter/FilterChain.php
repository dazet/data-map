<?php

namespace DataMap\Filter;

final class FilterChain
{
    /** @var string */
    private $key;

    /** @var Filter[] */
    private $filters;

    public function __construct(string $key, Filter ...$filters)
    {
        $this->key = $key;
        $this->filters = $filters;
    }

    public function key(): string
    {
        return $this->key;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function filter($value)
    {
        $result = $value;
        foreach ($this->filters as $filter) {
            $result = $filter($result);
        }

        return $result;
    }
}
