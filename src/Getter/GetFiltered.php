<?php

namespace DataMap\Getter;

use DataMap\Input\Input;

/**
 * Get value from input and pass it through sequence of functions
 */
final class GetFiltered implements Getter
{
    /** @var string */
    private $key;

    /** @var callable[] */
    private $filters;

    public function __construct(string $key, callable ...$filters)
    {
        $this->key = $key;
        $this->filters = $filters;
    }

    /**
     * @return mixed
     */
    public function __invoke(Input $input)
    {
        $value = $input->get($this->key);

        foreach ($this->filters as $filter) {
            $value = $filter($value);
        }

        return $value;
    }
}
