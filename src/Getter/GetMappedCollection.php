<?php

namespace DataMap\Getter;

use DataMap\Input\Input;
use Traversable;
use function array_map;
use function array_values;
use function is_array;
use function iterator_to_array;

final class GetMappedCollection implements Getter
{
    /** @var string */
    private $key;

    /** @var callable */
    private $mapper;

    public function __construct(string $key, callable $mapper)
    {
        $this->key = $key;
        $this->mapper = $mapper;
    }

    /**
     * @return mixed[]
     */
    public function __invoke(Input $input): array
    {
        $collection = $input->get($this->key);

        if ($collection instanceof Traversable) {
            $collection = iterator_to_array($collection);
        } elseif (is_array($collection)) {
            $collection = array_values($collection);
        } else {
            return [];
        }

        return array_map($this->mapper, $collection);
    }
}
