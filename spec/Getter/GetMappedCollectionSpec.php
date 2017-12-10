<?php

namespace spec\DataMap\Getter;

use DataMap\Input\ArrayInput;
use DataMap\Input\ObjectInput;
use DataMap\Mapper;
use PhpSpec\ObjectBehavior;

final class GetMappedCollectionSpec extends ObjectBehavior
{
    function it_maps_nested_collection_with_provided_callable()
    {
        $this->beConstructedWith(
            'users',
            function (array $user): string {
                return $user['name'] . ' ' . $user['surname'];
            }
        );

        $data = [
            'id' => 'something',
            'users' => [
                ['name' => 'John', 'surname' => 'Doe'],
                ['name' => 'Mary', 'surname' => 'Doe'],
            ],
        ];

        $this(new ArrayInput($data))->shouldBe(['John Doe', 'Mary Doe']);
    }

    function it_can_map_nested_collection_with_mapper()
    {
        $this->beConstructedWith('users', new Mapper(['name' => 'name', 'age' => 'more.age']));

        $data = [
            'id' => 'something',
            'users' => [
                ['name' => 'John', 'surname' => 'Doe', 'more' => ['age' => 33]],
                ['name' => 'Mary', 'surname' => 'Doe', 'more' => ['age' => 22]],
            ],
        ];

        $this(new ArrayInput($data))->shouldBe(
            [
                ['name' => 'John', 'age' => 33],
                ['name' => 'Mary', 'age' => 22],
            ]
        );
    }

    function it_maps_nested_traversable_item_with_provided_mapper()
    {
        $this->beConstructedWith('users', new Mapper(['name' => 'name', 'age' => 'more.age']));

        $userIterator = (function () {
            yield ['name' => 'John', 'surname' => 'Doe', 'more' => ['age' => 33]];
            yield ['name' => 'Mary', 'surname' => 'Doe', 'more' => ['age' => 22]];
        })();

        $data = new \stdClass();
        $data->users = $userIterator;

        $this(new ObjectInput($data))->shouldBe(
            [
                ['name' => 'John', 'age' => 33],
                ['name' => 'Mary', 'age' => 22],
            ]
        );
    }

    function it_returns_empty_array_when_given_entry_cannot_be_mapped()
    {
        $this->beConstructedWith('users', new Mapper(['name' => 'name', 'age' => 'more.age']));

        $data = ['id' => 'something', 'users' => 'John Doe, Mary Doe'];

        $this(new ArrayInput($data))->shouldBe([]);
    }
}
