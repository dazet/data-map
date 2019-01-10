# Data Mapper

Library for mapping and transforming data structures.

[![Build Status](https://travis-ci.org/dazet/data-map.svg?branch=master)](https://travis-ci.org/dazet/data-map)

## Defining mapper

`Mapper` configuration is defined as associative array `[Key => Getter, ...]` describing output structure.

`Key` defines property name and `Getter` is a function that extracts from input value for that property.

#### Examples

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
    // simply get `name` from input and assign to `firstName` property
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
`Getter` can be described as an interface:

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

There are 2 forms of defining map:

* `Getter` can be string which is shorthand for `new GetRaw('key')`.

* `Getter` can also be a closure or any other callable. It will receive `DataMap\Input\Input` as first argument and original input as second argument just in case it is needed. `Getter` interface is not required, it is just a hint as there are no function interfaces in PHP.

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

## Customizing or extending

`Mapper` consists of 3 components:

* `GetterMap` that describes mapping
* `InputWrapper` that allows to wraps input structure with proper `Input` implementation
* output `Formatter` that formats result as array or object of some class.

```php
<?php

$getterMap = [...];

$mapper = new Mapper($getterMap);

// which is equivalent of:
$mapper = new Mapper(
    $getterMap, 
    ArrayFormatter::default(), 
    FilteredWrapper::default()
);
```

Ways to customize `Mapper`:

* implement own `Input` and `InputWrapper` and extend default wrapper

  ```php
  $myWrapper = new MyWrapper();
  
  $mapper = new Mapper(
      $getterMap, 
      null, 
      FilteredWrapper::default()->withWrappers($myWrapper)
  );
  ```

* use only `MixedWrapper` for better performance when no recursion or transormations are needed

  ```php
  $mapper = new Mapper(
      $getterMap, 
      null, 
      MixedWrapper::default()
  );
  ```

* add custom transformation filter (see `FilteredInput`)

  ```php
  $mapper = new Mapper(
      $getterMap, 
      null, 
      FilteredWrapper::default()->withFilters([
          'replace' => new Filter('preg_replace', ['//', '', '$$'])
      ])
  );
  ```


## Input fetching

Input data can be gathered from different structure types using same `Input` interface.

#### `ArrayInput`

Wraps associative arrays.

```php
<?php
    
$input = new ArrayInput(['one' => 1]);

$input->get('one'); // 1
$input->get('two'); // null
$input->get('two', 'default'); // 'default'

$input->has('one'); // true
$input->has('two'); // false
```

#### `ObjectInput`

Wraps generic object and allows fetch data using object public interface: public properties or getters.

```php
<?php

class Example
{
    public $one = 1;
    private $two = 2;
    private $three = 3;
    
    public function two(): int
    {
        return $this->two;
    }
    
    public function getThree(): int
    {
        return $this->three;
    }
}

$input = new ObjectInput(new Example());

$input->get('one'); // 1
$input->get('two'); // 2
$input->get('three'); // 3
$input->get('four'); // null
$input->get('four', 'default'); // 'default'

$input->has('one'); // true
$input->has('four'); // false
```

#### `RecursiveInput`

`RecursiveInput` allows to fetch data from nested data structures using dot notation.

It decorates root element `Input`, but needs `Wrapper` to wrap nested data with proper `Input`.

```php
<?php

class Example
{
    public $one = ['nested' => 'nested one'];
    
    public function two(): object
    {
        return (object)['nested' => 'nested two'];
    }
};

$innerInput = new ObjectInput(new Example());
$input = new RecursiveInput($innerInput, MixedWrapper::default());

$input->get('one'); // ['nested' => 'nested one']
$input->get('one.nested'); // 'nested one'
$input->get('one.other'); // null
$input->get('two.nested'); // 'nested two'

$input->has('one'); // true
$input->has('one.nested'); // true
$input->has('one.other'); // false
```

#### `FilteredInput`

`FilteredInput` allows to transform input value using defined filter chain.

```php
<?php

$innerInput = new ArrayInput([
    'amount' => 123,
    'description' => '  string  ',
    'price' => 123.1234,
]);

$input = new FilteredInput($innerInput, FilterChainParser::default());

$input->get('amount | string'); // '123'
$input->get('description | trim | upper'); // 'STRING'
$input->get('description | integer'); // null
$input->get('price | round'); // 123.0
$input->get('description | round'); // null
$input->get('price | round 2'); // 123.12
$input->get('price | ceil | integer'); // 124
```

##### Default filters

* `string`: cast value to string if possible or return null

* `int`, `integer`: cast to integer or return null

* `float`: cast to float or return null

* `bool`, `boolean`: try to resolve value as boolean or return null

* `array`: cast value to array if possible (from array or iterable) or return null

* `explode [delimiter=","]`: explode string using delimiter

  e.g. 1: default explode by comma `string | explode`

  e.g. 2: explode by custom string `string | explode "-"`

* `implode [delimiter=","]`: implode array of strings using delimiter
  e.g. 1: default implode by comma `array | implode`

  e.g. 2: explode by custom string `array | implode "-"`

* `upper`: upper case string

* `lower`: lower case string

* `trim`, `ltrim`, `rtrim`: trim string

* `format`: format value as string using `sprintf`
  e.g. 1: `string | format "string: %s"`
  e.g. 2: `float | format "price: $%01.2f"` transforms `12.3499` to `'price: $12.35'`

* `replace [search] [replace=""]`: replace substring in string like `str_replace` function
  e.g. 1: `string | replace "remove me"` replaces `'remove me'` with empty sting
  e.g. 2: `string | replace "red" "green"` transforms `'tests are red'` to `'tests are green'`

* `strip_tags`: same as `strip_tags` function

* `number_format [decimals=2] [decimal_point="."] [thousands_separator=","]`: same as `number_format` function

* `round [precision=0]`: same as `round` function

* `floor`

* `ceil`

* `datetime`: try to transform value to `DateTimeImmutable` or return null

* `date_format [format="Y-m-d H:i:s"]`: try to transform value to datetime and format as string or return null when value cannot be transformed

* `date_modify [modifier]`: try to transform value to `DateTimeImmutable` and then transform it using modifier `$datetime->modify($modifier)`, e.g.: `date_string | date_modify "+1 day"`

* `timestamp`: try to transform value to datetime and then to timestamp or return null

* `json_encode`

* `json_decode`

* `count`: return count for array or `Countable` or null when not countable

* `if_null [then]`: define default value when mapped value is null
  e.g. `maybe_string | string | if_null "default"`

* `if_empty [then]`: define default value when mapped value is empty
  e.g. `maybe_string | string | if_empty "default"`

##### Function as transformation

Default configuration of `FilterChainParser` allows use any PHP function as transformation. By default mapped value is passed as first argument to that function optionally followed by other arguments defined in filter config. It is also possible to define different argument position of mapped value using `$$` as a placeholder.

###### Examples:

* calculate md5 of mapped value: `key | string | md5`
* wrap string after 20 characters: `key | string | wordwrap 20`
* custom argument position of mapped value `key | string | preg_replace "/\s+/" " " $$`

##### Defining custom filters


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
```
 
### `Array` -> `Object`

​```php
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
