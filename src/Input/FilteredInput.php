<?php

namespace DataMap\Input;

use DataMap\Filter\FilterChainParser;

final class FilteredInput implements Input
{
    /** @var Input */
    private $inner;

    /** @var FilterChainParser */
    private $parser;

    public function __construct(Input $inner, FilterChainParser $parser)
    {
        $this->inner = $inner;
        $this->parser = $parser;
    }

    /**
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $filter = $this->parser->parse($key);
        $value = $this->inner->get($filter->key());

        return $filter->filter($value);
    }

    public function has(string $key): bool
    {
        return $this->inner->has($this->parser->parse($key)->key());
    }
}
