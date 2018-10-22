<?php

namespace spec\DataMap;

use DataMap\Getter\GetBoolean;
use DataMap\Getter\GetFloat;
use DataMap\Getter\GetInteger;
use DataMap\Getter\GetJoinedStrings;
use DataMap\Getter\GetMappedCollection;
use DataMap\Getter\GetMappedFlatCollection;
use DataMap\Getter\GetString;
use DataMap\Getter\GetTranslated;
use DataMap\Input\Input;
use DataMap\Mapper;
use DataMap\Output\ObjectConstructor;
use DataMap\Output\ObjectHydrator;
use PhpSpec\ObjectBehavior;
use spec\DataMap\Stub\UserDto;
use spec\DataMap\Stub\UserValue;

final class MapperSpec extends ObjectBehavior
{
    function it_maps_simple_array()
    {
        $this->beConstructedWith(
            [
                'id' => 'user_id',
                'name' => 'user_name',
                'age' => 'years',
            ]
        );

        $this
            ->map(
                [
                    'user_id' => 123,
                    'user_name' => 'Flip',
                    'years' => 65,
                    'not_important' => 'I like pancakes.',
                ]
            )
            ->shouldReturn(
                [
                    'id' => 123,
                    'name' => 'Flip',
                    'age' => 65,
                ]
            );
    }

    function it_maps_simple_object()
    {
        $this->beConstructedWith(
            [
                'id' => 'userId',
                'name' => 'userName',
                'age' => 'years',
            ]
        );

        $user = new \stdClass();
        $user->userId = 123;
        $user->userName = 'Flip';
        $user->years = 65;
        $user->notImportant = 'I like pancakes.';

        $this->map($user)
            ->shouldReturn(
                [
                    'id' => 123,
                    'name' => 'Flip',
                    'age' => 65,
                ]
            );
    }

    function it_maps_array_recursively_using_dot_notation()
    {
        $this->beConstructedWith(
            [
                'id' => 'data.id',
                'name' => 'data.name',
                'nested' => 'some.deeply.nested.data',
                'not_present' => 'not_present.deeply.nested.key',
            ]
        );

        $this
            ->map(
                [
                    'data' => [
                        'id' => 124,
                        'name' => 'Flap',
                        'age' => 50,
                    ],
                    'some' => [
                        'deeply' => [
                            'nested' => [
                                'data' => 'value',
                            ],
                        ],
                    ],
                    'not_important' => 'I like pancakes.',
                ]
            )
            ->shouldReturn(
                [
                    'id' => 124,
                    'name' => 'Flap',
                    'nested' => 'value',
                    'not_present' => null,
                ]
            );
    }

    function it_maps_object_recursively_using_dot_notation()
    {
        $this->beConstructedWith(
            [
                'id' => 'data.id',
                'name' => 'data.name',
                'nested' => 'some.deeply.nested.data',
                'not_present' => 'not_present.deeply.nested.key',
            ]
        );

        $user = new \stdClass();

        $user->data = new \stdClass();
        $user->data->id = 124;
        $user->data->name = 'Flap';
        $user->data->age = 50;

        $user->some = new \stdClass();
        $user->some->deeply = new \stdClass();
        $user->some->deeply->nested = new \stdClass();
        $user->some->deeply->nested->data = 'value';
        $user->notImportant = 'I like pancakes.';

        $this
            ->map($user)
            ->shouldReturn(
                [
                    'id' => 124,
                    'name' => 'Flap',
                    'nested' => 'value',
                    'not_present' => null,
                ]
            );
    }

    function it_favours_concrete_key_over_recursive_key_in_arrays()
    {
        $this->beConstructedWith(
            [
                'first' => 'first.nested.key',
                'second' => 'second.nested.key',
            ]
        );

        $this
            ->map(
                [
                    'first.nested.key' => 'concrete',
                    'first' => [
                        'nested' => [
                            'key' => 'recursive',
                        ],
                    ],
                    'second' => [
                        'nested' => [
                            'key' => 'recursive',
                        ],
                    ],
                    'second.nested.key' => 'concrete',
                ]
            )
            ->shouldReturn(
                [
                    'first' => 'concrete',
                    'second' => 'concrete',
                ]
            );
    }

    function it_does_not_fail_when_map_key_has_dot_at_the_end_or_the_beginning()
    {
        $this->beConstructedWith(
            [
                'first' => 'first.',
                'second' => 'second.',
                'third' => '.third',
                'fourth' => '.fourth',
            ]
        );

        $this
            ->map(
                [
                    'first' => 'no',
                    'second.' => 'yes',
                    'third' => 'no',
                    '.fourth' => 'yes',
                ]
            )
            ->shouldReturn(
                [
                    'first' => null,
                    'second' => 'yes',
                    'third' => null,
                    'fourth' => 'yes',
                ]
            );
    }

    function it_returns_null_when_trying_recursively_map_scalar_value()
    {
        $this->beConstructedWith(
            [
                'parameter' => 'nested.parameter',
                'nested' => 'nested',
            ]
        );

        $this
            ->map(
                [
                    'nested' => 'no',
                ]
            )
            ->shouldReturn(
                [
                    'parameter' => null,
                    'nested' => 'no',
                ]
            );
    }

    function it_can_create_entry_through_custom_callable_getter()
    {
        $this->beConstructedWith(
            [
                'a' => 'a',
                'b' => 'b',
                'c' => 'c',
                'a + b' => function (Input $input): int {
                    return $input->get('a') + $input->get('b');
                },
                'a + c' => function (Input $input): int {
                    return $input->get('a') + $input->get('c');
                },
                'b + c' => function (Input $input): int {
                    return $input->get('b') + $input->get('c');
                },
            ]
        );

        $this
            ->map(['a' => 5, 'b' => 10, 'c' => 15])
            ->shouldReturn(
                [
                    'a' => 5,
                    'b' => 10,
                    'c' => 15,
                    'a + b' => 15,
                    'a + c' => 20,
                    'b + c' => 25,
                ]
            );
    }

    function it_can_map_nested_structures_with_other_mapper()
    {
        $characterMapper = new Mapper(
            [
                'fullname' => new GetJoinedStrings(' ', 'name', 'surname'),
            ]
        );

        $this->beConstructedWith(
            [
                'movie' => 'name',
                'characters' => new GetMappedCollection('characters', $characterMapper),
            ]
        );

        $this
            ->map(
                [
                    'name' => 'Lucky Luke',
                    'characters' => [
                        ['name' => 'Lucky', 'surname' => 'Luke'],
                        ['name' => 'Joe', 'surname' => 'Dalton'],
                        ['name' => 'William', 'surname' => 'Dalton'],
                        ['name' => 'Jack', 'surname' => 'Dalton'],
                        ['name' => 'Averell', 'surname' => 'Dalton'],
                    ],
                ]
            )
            ->shouldReturn(
                [
                    'movie' => 'Lucky Luke',
                    'characters' => [
                        ['fullname' => 'Lucky Luke'],
                        ['fullname' => 'Joe Dalton'],
                        ['fullname' => 'William Dalton'],
                        ['fullname' => 'Jack Dalton'],
                        ['fullname' => 'Averell Dalton'],
                    ],
                ]
            );
    }

    function it_can_flatten_nested_collections()
    {
        $charactersMapper = new Mapper(
            [
                new GetJoinedStrings(' ', 'name', 'surname'),
            ]
        );

        $this->beConstructedWith(
            [
                'movie' => 'name',
                'characters' => new GetMappedFlatCollection('characters', $charactersMapper),
            ]
        );

        $this
            ->map(
                [
                    'name' => 'Lucky Luke',
                    'characters' => [
                        ['name' => 'Lucky', 'surname' => 'Luke'],
                        ['name' => 'Joe', 'surname' => 'Dalton'],
                        ['name' => 'William', 'surname' => 'Dalton'],
                        ['name' => 'Jack', 'surname' => 'Dalton'],
                        ['name' => 'Averell', 'surname' => 'Dalton'],
                    ],
                ]
            )
            ->shouldReturn(
                [
                    'movie' => 'Lucky Luke',
                    'characters' => [
                        'Lucky Luke',
                        'Joe Dalton',
                        'William Dalton',
                        'Jack Dalton',
                        'Averell Dalton',
                    ],
                ]
            );
    }

    function it_can_use_predefined_getters()
    {
        $this->beConstructedWith(
            [
                'name' => new GetJoinedStrings(' ', 'name', 'surname'),
                'integer' => new GetInteger('age'),
                'float' => new GetFloat('age'),
                'valid' => new GetBoolean('valid'),
                'validStr' => new GetString('valid'),
                'status' => new GetTranslated('valid', [0 => 'not active', 1 => 'active']),
            ]
        );

        $this
            ->map(
                [
                    'name' => 'John',
                    'surname' => 'Doe',
                    'age' => '18',
                    'valid' => 1,
                ]
            )
            ->shouldBe(
                [
                    'name' => 'John Doe',
                    'integer' => 18,
                    'float' => 18.0,
                    'valid' => true,
                    'validStr' => '1',
                    'status' => 'active',
                ]
            );

        $this
            ->map(
                [
                    'name' => 'Mary',
                    'surname' => 'Doe',
                    'age' => '17.5',
                    'valid' => 0,
                ]
            )
            ->shouldBe(
                [
                    'name' => 'Mary Doe',
                    'integer' => 17,
                    'float' => 17.5,
                    'valid' => false,
                    'validStr' => '0',
                    'status' => 'not active',
                ]
            );
    }

    function it_can_hydrate_simple_object_with_public_attributes()
    {
        $this->beConstructedWith(
            [
                'id' => new GetString('user.id'),
                'name' => new GetJoinedStrings(' ', 'user.name', 'user.surname'),
                'age' => new GetInteger('user.years'),
            ],
            new ObjectHydrator(new UserDto())
        );

        $input = [
            'user' => [
                'id' => 'c0933b83-11df-45aa-9b7e-1d2b6e4f5053',
                'name' => 'John',
                'surname' => 'Doe',
                'years' => '33',
            ],
        ];

        $expected = new UserDto();
        $expected->id = 'c0933b83-11df-45aa-9b7e-1d2b6e4f5053';
        $expected->name = 'John Doe';
        $expected->age = 33;

        $this->map($input)->shouldBeLike($expected);
    }

    function it_can_hydrate_simple_object_with_public_attributes_defined_by_class_name()
    {
        $this->beConstructedWith(
            [
                'id' => new GetString('user.id'),
                'name' => new GetJoinedStrings(' ', 'user.name', 'user.surname'),
            ],
            new ObjectHydrator(UserDto::class)
        );

        $input = [
            'user' => [
                'id' => 'c0933b83-11df-45aa-9b7e-1d2b6e4f5053',
                'name' => 'John',
                'surname' => 'Doe',
            ],
        ];

        $expected = new UserDto();
        $expected->id = 'c0933b83-11df-45aa-9b7e-1d2b6e4f5053';
        $expected->name = 'John Doe';

        $this->map($input)->shouldBeLike($expected);
    }

    function it_can_construct_output_object()
    {
        $this->beConstructedWith(
            [
                'id' => new GetString('user.id'),
                'name' => new GetJoinedStrings(' ', 'user.name', 'user.surname'),
                'age' => new GetInteger('user.years'),
            ],
            new ObjectConstructor(UserValue::class)
        );

        $input = [
            'user' => [
                'id' => 'c0933b83-11df-45aa-9b7e-1d2b6e4f5053',
                'name' => 'John',
                'surname' => 'Doe',
                'years' => '33',
            ],
        ];

        $this->map($input)->shouldBeLike(new UserValue('c0933b83-11df-45aa-9b7e-1d2b6e4f5053', 'John Doe', 33));
    }

    function it_can_transform_result_value_through_predefined_pipe_functions()
    {
        $data = [
            'string_float' => '123.123',
            'float' => 123.1234,
            'spaced_string' => '  gimme some space     ',
            'html_string' => '<h1>Hello world!</h1>',
            'string_list' => 'apple,orange,banana',
            'array_list' => ['apple', 'orange', 'banana'],
            'true_string' => '1',
            'true_int' => 1,
            'false_string' => '0',
            'false_int' => 0,
            'date_string' => '1410-07-15 12:00',
            'json_string' => '{"message": "Hello world!"}',
        ];

        $this->beConstructedWith([
            'string_to_int' => 'string_float | int',
            'string_to_integer' => 'string_float | integer',
            'string_to_float' => 'string_float | float',
            'string_to_true' => 'true_string | bool',
            'string_to_false' => 'false_string | boolean',
            'int_to_true' => 'true_int | bool',
            'int_to_false' => 'false_int | bool',
            'float_round' => 'float | round',
            'float_floor' => 'float | floor',
            'float_ceil' => 'float | ceil',
            'date_formatted' => 'date_string | date_format "d m Y"',
            'trim' => 'spaced_string | trim',
            'trim_left' => 'spaced_string | ltrim',
            'trim_right' => 'spaced_string | rtrim',
            'trim_and_upper' => 'spaced_string | trim | upper',
            'trim_and_replace' => 'spaced_string | trim | replace some more',
            'trim_and_format' => 'spaced_string | trim | format "-- %s --"',
            'strip_html' => 'html_string | strip_tags',
            'list_explode' => 'string_list | explode ,',
            'list_explode_to_json' => 'string_list | explode , | json_encode',
            'list_implode' => 'array_list | implode " / "',
            'json_decoded' => 'json_string | json_decode',
        ]);

        $this->map($data)->shouldReturn([
            'string_to_int' => 123,
            'string_to_integer' => 123,
            'string_to_float' => 123.123,
            'string_to_true' => true,
            'string_to_false' => false,
            'int_to_true' => true,
            'int_to_false' => false,
            'float_round' => 123.0,
            'float_floor' => 123.0,
            'float_ceil' => 124.0,
            'date_formatted' => '15 07 1410',
            'trim' => 'gimme some space',
            'trim_left' => 'gimme some space     ',
            'trim_right' => '  gimme some space',
            'trim_and_upper' => 'GIMME SOME SPACE',
            'trim_and_replace' => 'gimme more space',
            'trim_and_format' => '-- gimme some space --',
            'strip_html' => 'Hello world!',
            'list_explode' => \explode(',', 'apple,orange,banana'),
            'list_explode_to_json' => \json_encode(\explode(',', 'apple,orange,banana')),
            'list_implode' => 'apple / orange / banana',
            'json_decoded' => ['message' => 'Hello world!'],
        ]);
    }

    function it_can_transform_result_value_through_predefined_date_pipes()
    {
        $data = [
            'date_string' => '1410-07-15 12:00',
        ];

        $this->beConstructedWith([
            'date_object' => 'date_string | datetime',
            'date_modified' => 'date_string | date_modify "+3 days"',
        ]);

        $this->map($data)->shouldBeLike([
            'date_object' => new \DateTimeImmutable($data['date_string']),
            'date_modified' => (new \DateTimeImmutable($data['date_string']))->modify('+3 days'),
        ]);
    }
}
