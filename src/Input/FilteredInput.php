<?php declare(strict_types=1);

namespace DataMap\Input;

use DataMap\Filter\InputFilterParser;

final class FilteredInput implements Input
{
    /** @var Input */
    private $inner;

    /** @var InputFilterParser */
    private $parser;

    public function __construct(Input $inner, InputFilterParser $parser)
    {
        $this->inner = $inner;
        $this->parser = $parser;
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $filter = $this->parser->parse($key);
        $value = $this->inner->get($filter->key());

        return $filter->transform($value);
    }

    public function has(string $key): bool
    {
        return $this->inner->has($this->parser->parse($key)->key());
    }
}
