<?php

namespace spec\DataMap\Getter;

use DataMap\Input\ArrayInput;
use DataMap\Mapper;
use PhpSpec\ObjectBehavior;

final class GetMappedFlatCollectionSpec extends ObjectBehavior
{
    function it_maps_and_flattens_nested_collection()
    {
        $this->beConstructedWith('users', new Mapper(['name', 'surname']));

        $data = [
            'id' => 'something',
            'users' => [
                ['name' => 'John', 'surname' => 'Doe', 'more' => ['age' => 33]],
                ['name' => 'Mary', 'surname' => 'Doe', 'more' => ['age' => 22]],
            ],
        ];

        $this(new ArrayInput($data))->shouldBe(['John', 'Doe', 'Mary', 'Doe']);
    }

    function it_maps_and_flattens_nested_collection_when_result_is_associative_array()
    {
        $this->beConstructedWith('users', new Mapper(['name' => 'name', 'surname' => 'surname']));

        $data = [
            'id' => 'something',
            'users' => [
                ['name' => 'John', 'surname' => 'Doe', 'more' => ['age' => 33]],
                ['name' => 'Mary', 'surname' => 'Doe', 'more' => ['age' => 22]],
            ],
        ];

        $this(new ArrayInput($data))->shouldBe(['John', 'Doe', 'Mary', 'Doe']);
    }

    function it_returns_empty_array_when_nested_collection_is_not_a_collection()
    {
        $this->beConstructedWith('users', new Mapper(['name', 'surname']));

        $data = ['id' => 'something', 'users' => 'John, Mary'];

        $this(new ArrayInput($data))->shouldBe([]);
    }
}
