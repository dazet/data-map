# Data Mapper

Library for mapping data structures.

[![Build Status](https://travis-ci.org/dazet/data-map.svg?branch=master)](https://travis-ci.org/dazet/data-map)

## Defining mapper

`Mapper` configuration is defined as association `[Key => Getter, ...]` describing required output structure.

```php
<?php

use DataMap\Getter\GetInteger;
use DataMap\Mapper;
use DataMap\Input\Input;

// Input structure is:
$input = [
    'name' => 'John',
    'surname' => 'Doe',
    'date_birth' => '1970-01-01',
    'address' => [
        'street' => 'Foo Street',
        'city' => [
            'name' => 'Bar Town',
            'country' => 'Neverland',
        ],
    ],
    'age' => '47',
];

// Required output structore is:
$output = [
    'firstName' => 'John',
    'fullName' => 'John Doe',
    'street' => 'Foo Street',
    'city' => 'Bar Town',
    'age' => 47,
    'birth' => new \DateTimeImmutable('1970-01-01'),
];

// Then mapping definition is:
$mapper = new Mapper([
    // simply get `name` from input and assign to `firstName` key
    'firstName' => 'name',
    // join name with surname and assign to `fullName`
    'fullName' => function (Input $input): string {
        return $input->get('name') . ' ' . $input->get('surname');
    },
    // get street and city name from nested structure
    'street' => 'address.street',
    'city' => 'address.city.name',
    // get `age` from input, cast to integer and assign to `age`
    'age' => new GetInteger('age'),
    // get date as `\DateTimeImmutable` object
    'birth' => new GetDate('date_birth'),
]);

// You can map $input to $output:
$output = $mapper->map($input);

// You can map collection of entries:
$outputCollection = array_map($mapper, $inputCollection);

// You can extend mapper definition:
$extendedMapper = $mapper->withAddedMap(['country' => 'address.city.country']);

```
`Key` defines property name of output structure.

`Getter` is a function that generally can be described by interface:
```php
<?php 

use DataMap\Input\Input;

interface Getter
{
    /**
     * @return mixed
     */
    public function __invoke(Input $input);
}
```

`Getter` can be string which is shorthand for `new GetRaw('key')`.

`Getter` can also be a closure or any other callable (`Getter` interface is not required).
It receives `DataMap\Input\Input` as first argument and original input as second argument.

`Getter` key by default supports recursive lookup dot notation,
so you can use `nested.key` instead of `$array['nested']['key']` or `$object->nested->key` or `$object->getNested()->getKey()`.

## Predefined Getters

* `new GetRaw($key, $default)` - gets value as it is. 
* `new GetString($key, $default)` - gets value and casts to string (if possible) or `$default`. 
* `new GetInteger($key, $default)` - gets value and casts to integer (if possible) or `$default`. 
* `new GetFloat($key, $default)` - gets value and casts to float (if possible) or `$default`. 
* `new GetBoolean($key, $default)` - gets value and casts to boolean (`true`, `false`, `0`, `1`, `'0'`, `'1'`) or `$default`. 
* `new GetDate($key, $default)` - gets value and transform to `\DateTimeImmutable` (if possible) or `$default`. 
* `new GetJoinedStrings($glue, $key1, $key2, ...)` - gets string value for given keys an join it using `$glue`. 
* `new GetMappedCollection($key, $callback)` - gets collection under given `$key` and maps it with `$callback` or return `[]` if entry cannot be mapped. 
* `new GetMappedFlatCollection($key, $callback)` - similar to `GetMappedCollection` but result is flattened.
* `new GetTranslated($key, $map, $default)` - gets value and translates it using provided associative array (`$map`) or `$default` when translation for value is not available.

## Output formatting

MapperInterface output depends on `Formatter` used by `Mapper`.

Built-in formatters:

#### `ArrayFormatter`

```php
<?php
$mapper = new Mapper($map);
// same as:
$mapper = new Mapper($map, new ArrayFormatter());
```

#### `ObjectConstructor`

```php
<?php
// by class constructor:
$mapper = new Mapper($map, new ObjectConstructor(SomeClass::class));

// by static method:
$mapper = new Mapper($map, new ObjectConstructor(SomeClass::class, 'method'));
``` 

Tries to create new instance of object using regular constructor. Map keys are matched with constructor parameters by variable name.

#### `ObjectHydrator`

```php
<?php
// by class constructor:
$mapper = new Mapper($map, new ObjectHydrator(new SomeClass()));

// by static method:
$mapper = new Mapper($map, new ObjectHydrator(SomeClass::class));
```

Tries to hydrate instance of object by setting public properties values or using setters (`setSomething` or `withSomething` assuming immutability).

## Input wrapping

Input structure is wrapped with proper object implementing `Input` interface.

Built-in input types:
* `ArrayInput` gets values from associative array.  
* `ObjectInput` gets values from object public properties or getters.

Any `Input` can be decorated with `RecursiveInput` so keys can be recursive.

## Examples

### `Array` -> `Array`

```php
<?php

use DataMap\Mapper;

$response = [
    'data' => [
        'user' => ['id' => 'abc-123', 'name' => 'John'],
    ],
];

$responseMapper = new Mapper([
    'id' => 'data.user.id',
    'name' => 'data.user.name',
]);

$user = $responseMapper->map($response);

// array (
//     'id' => 'abc-123',	
//     'name' => 'John',
// )
``` 

### `Object` -> `Array`

```php
<?php

use DataMap\Input\Input;
use DataMap\Mapper;
use DataMap\Output\ObjectHydrator;

class UserDto
{
    public $id;
    public $name;
}

$user = new UserDto();
$user->id = '123';
$user->name = 'John Doe';

$mapper = new Mapper(
    [
        'name' => 'name',
        'name_id' => function (Input $input): string {
            return "{$input->get('name')} [{$input->get('id')}]";
        }
    ]
);

$result = $mapper->map($user);

// array (
//     'name' => 'John Doe',
//     'name_id' => 'John Doe [123]',
// )
````

### `Array` -> `Object`

```php
<?php

use DataMap\Getter\GetInteger;
use DataMap\Mapper;
use DataMap\Output\ObjectHydrator;

$response = [
    'data' => [
        'user' => ['id' => 'abc-123', 'name' => 'John'],
    ],
];

class UserDto
{
    public $id;
    public $name;
    public $age;
}

$responseMapper = new Mapper(
    [
        'id' => 'data.user.id',
        'name' => 'data.user.name',
        'age' => new GetInteger('data.user.name', 18),
    ],
    new ObjectHydrator(UserDto::class)
);

$user = $responseMapper->map($response);

// UserDto (
//     'id' => 'abc-123',
//     'name' => 'John',
//     'age' => 18,
// )
```
