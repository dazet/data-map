# Data Mapper

Library for mapping and transforming data structures.

[![Build Status](https://travis-ci.org/dazet/data-map.svg?branch=master)](https://travis-ci.org/dazet/data-map)

## Defining mapper

`Mapper` configuration is defined as association `[Key1 => Getter1, Key2 => Getter2 ...]` describing output structure.

`Key` defines property name in output structure and `Getter` is a function that extracts value from input.

##### Examples

```php
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
    'firstName' => 'name',                          // simply get `name` from input and assign to `firstName` property
    'fullName' => function (Input $input): string {
        return $input->get('name') . ' ' . $input->get('surname');
    },                                              // use Closure as Getter function
    'street' => 'address.street',                   // get values from nested structures
    'city' => 'address.city.name',
    'age' => new GetInteger('age'),                 // use one of predefined getters
    'birth' => new GetDate('date_birth'),           // get date as `\DateTimeImmutable` object
]);

// Map $input to $output:
$output = $mapper->map($input);

// Map collection of entries:
$outputCollection = array_map($mapper, $inputCollection);

// Extend mapper definition:
$newMapper = $mapper->withAddedMap(['country' => 'address.city.country']);
```

`Getter` generally can be described as interface:

```php
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
* `Getter` can also be a closure or any other callable. It will receive `DataMap\Input\Input` as first argument and original input as second argument. 
  `Getter` interface is not required, it's just a hint.

## Predefined Getters

#### `new GetRaw($key, $default)`

Get value by property path without additional transformation.

```php
$mapper = new Mapper([
    'name' => new GetRaw('first_name'),
    // same as:  
    'name' => 'first_name',  
]);
```
#### `new GetString($key, $default)`

Gets value and casts to string (if possible) or returns `$default`.

```php
$mapper = new Mapper([
    'name' => new GetString('username', 'anonymous'),
]);
```

#### `new GetInteger($key, $default)`

Gets value and casts to integer (if possible) or `$default`.

```php
$mapper = new Mapper([
    'age' => new GetInteger('user.age', null),
]);
```

#### `new GetFloat($key, $default)`

Gets value and casts to float (if possible) or `$default`. 

#### `new GetBoolean($key, $default)`

Gets value and casts to boolean (`true`, `false`, `0`, `1`, `'0'`, `'1'`) or `$default`. 

#### `new GetDate($key, $default)`

Gets value and transform to `\DateTimeImmutable` (if possible) or `$default`.
 
#### `new GetJoinedStrings($glue, $key1, $key2, ...)`

Gets string value for given keys an join it using `$glue`.

```php
$mapper = new Mapper([
    'fullname' => new GetJoinedStrings(' ', 'user.name', 'user.surname'),
]);
```

#### `new GetMappedCollection($key, $callback)`

Gets collection under given `$key` and maps it with `$callback` or return `[]` if entry cannot be mapped.

```php
$characterMapper = new Mapper([
    'fullname' => new GetJoinedStrings(' ', 'name', 'surname'),
] );

$movieMapper = new Mapper([
    'movie' => 'name',
    'characters' => new GetMappedCollection('characters', $characterMapper),
]);

$mapper->map([
    'name' => 'Lucky Luke',
    'characters' => [
        ['name' => 'Lucky', 'surname' => 'Luke'],
        ['name' => 'Joe', 'surname' => 'Dalton'],
        ['name' => 'William', 'surname' => 'Dalton'],
        ['name' => 'Jack', 'surname' => 'Dalton'],
        ['name' => 'Averell', 'surname' => 'Dalton'],
    ],
]);

// result:
[
   'movie' => 'Lucky Luke',
   'characters' => [
       ['fullname' => 'Lucky Luke'],
       ['fullname' => 'Joe Dalton'],
       ['fullname' => 'William Dalton'],
       ['fullname' => 'Jack Dalton'],
       ['fullname' => 'Averell Dalton'],
   ],
];
```
 
#### `new GetMappedFlatCollection($key, $callback)`

Similar to `GetMappedCollection` but result is flattened.

#### `new GetTranslated($key, $map, $default)`

Gets value and translates it using provided associative array (`$map`) or `$default` when translation for value is not available.

```php
$mapper = new Mapper([
    'agree' => new GetTranslated('agree', ['yes' => true, 'no' => false], false), 
]);

$mapper->map(['agree' => 'yes']) === ['agree' => true];
$mapper->map(['agree' => 'no']) === ['agree' => false];
$mapper->map(['agree' => 'maybe']) === ['agree' => null];
```

#### `GetFiltered::from('key')->...`

Gets value and transforms it through filters pipeline.

```php
$mapper = new Mapper([
    'text' => GetFiltered::from('html')->string()->stripTags()->trim()->ifNull('[empty]'),
    'time' => GetFiltered::from('datetime')->dateFormat('H:i:s'),
    'date' => GetFiltered::from('time_string')->date(),
    'amount' => GetFiltered::from('amount_string')->float()->round(2),
    'amount_int' => GetFiltered::from('amount_string')->round()->int()->ifNull(0),
]);
```

Using function as filter:

```php
$greeting = function (string $name): string {
    return "Hello {$name}!";
};

$mapper = new Mapper([
    'greet' => GetFiltered::from('name')->string()->with($greeting),
]);

$mapper->map(['name' => 'John']); // result: ['greet' => 'Hello John!']
```

Regular filters will not be called when value becomes `null`, with exceptions of `ifNull`, `ifEmpty` and `withNullable`.

Custom `null` handling filter:

```php
$requireInt = function ($value): int {
    if (!is_int($value)) {
        throw new InvalidArgumentException('I require int!');
    }

    return $value;
};

$mapper = new Mapper([
    'must_be_int' => GetFiltered::from('number')->int()->withNullable($requireInt),
]);

$mapper->map(['number' => 'x']); // throws InvalidArgumentException
$mapper->map(['number' => 1]); // returns ['required_int' => 1]
```

## Input fetching

`Input` interface defines common abstraction for fetching data from different data structures.
Mapping can be defined without going into details about underlying data type. 

It also allows to create input decorators for additional input processing, like data filtering, transformation, data traversing etc.

#### `ArrayInput`

Wraps associative arrays and ArrayAccess objects.

```php
$array = ['one' => 1];
$input = new ArrayInput($array);

$input->get('key'); // is translated to: $array['key'] ?? null
$input->get('one'); // 1
$input->get('two'); // null
$input->get('two', 'default'); // 'default'

$input->has('one'); // true
$input->has('two'); // false
```

#### `ObjectInput`

Wraps generic object and allows fetch data using object public interface: public properties or getters (getter is a public method without parameters that returns some value).

Fetch method for key `name` is resolved in the following order:
* check for public property `name`
* check for getter `name()`
* check for getter `getName()`
* check for getter `isName()`

```php
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

$object = new Example();
$input = new ObjectInput($object);

$input->get('one'); // 1 (property $object->one)
$input->get('two'); // 2 (getter $object->())
$input->get('three'); // 3 (getter $object->getThree())
$input->get('four'); // null (no property, no getter)
$input->get('four', 'default'); // 'default'

$input->has('one'); // true
$input->has('four'); // false
```

#### `RecursiveInput`

`RecursiveInput` allows to traverse trees od data using dot notation (`$input->get('root.branch.leaf')`).
It decorates `Input` (current leaf) and requires `Wrapper` to wrap with proper `Input` next visited leafs (which can be arrays or objects).

```php
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

`FilteredInput` is another `Input` decorator that allows to transform data after it is extracted from inner structure.

```php
$innerInput = new ArrayInput([
    'amount' => 123,
    'description' => '  string  ',
    'price' => 123.1234,
]);

$input = new FilteredInput($innerInput, InputFilterParser::default());

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

Default configuration of `InputFilterParser` allows use any PHP function as transformation. 
By default mapped value is passed as first argument to that function optionally followed by other arguments defined in filter config. 
It is also possible to define different argument position of mapped value using `$$` as a placeholder.

###### Examples:

* calculate md5 of mapped value: `key | string | md5`
* wrap string after 20 characters: `key | string | wordwrap 20`
* custom argument position of mapped value `key | string | preg_replace "/\s+/" " " $$`

## Output formatting

MapperInterface output depends on `Formatter` used by `Mapper`.

Built-in formatters:

#### `ArrayFormatter`

```php
$mapper = new Mapper($map);
// same as:
$mapper = new Mapper($map, new ArrayFormatter());
```

#### `ObjectConstructor`

```php
// by class constructor:
$mapper = new Mapper($map, new ObjectConstructor(SomeClass::class));

// by static method:
$mapper = new Mapper($map, new ObjectConstructor(SomeClass::class, 'method'));
```

Tries to create new instance of object using regular constructor. Map keys are matched with constructor parameters by variable name.

#### `ObjectHydrator`

```php
// by class constructor:
$mapper = new Mapper($map, new ObjectHydrator(new SomeClass()));

// by static method:
$mapper = new Mapper($map, new ObjectHydrator(SomeClass::class));
```

Tries to hydrate instance of object by setting public properties values or using setters (`setSomething` or `withSomething` assuming immutability).

## Customizing and extending

`Mapper` consists of 3 components:

* `GetterMap` that describes mapping
* `InputWrapper` that allows to wraps input structure with proper `Input` implementation
* output `Formatter` that formats result as array or object of some class.

```php
$getterMap = [...];

$mapper = new Mapper($getterMap);

// which is equivalent of:
$mapper = new Mapper(
    $getterMap, 
    ArrayFormatter::default(), 
    FilteredWrapper::default()
);
```

## Customizing `Mapper`

#### Implement `Input` and `InputWrapper` to extract data from various sources

```php
interface Attributes
{
    public function getAttribute($key, $default = null);
}

class AttributesInput implements Input
{
    /** @var Attribiutes */
    private $attributes;
  
    public function get(string $key, $default = null)
    {
        return $this->attributes->getAttribute($key, $default);
    }
    // ...    
}

class AttributesWrapper implements Wrapper
{
    public function supportedTypes(): array
    {
        return [Attributes::class]
    }
  
    public function wrap($data): Input
    {
        return new AttributesInput($data);
    }
}

$mapper = new Mapper(
    $getterMap, 
    null, 
    FilteredWrapper::default()->withWrappers(new AttributesWrapper())
);
```

#### Use only `MixedWrapper` for better performance
 
When not needed fetching data from nested structures and input piped filters (defined as string).

```php
$mapper = new Mapper(
    $getterMap, 
    null, 
    MixedWrapper::default()
);
```

#### Custom filters (see `FilteredInput`):

```php
$mapper = new Mapper(
    [
        'slug' => 'title | replace "/[\PL]+/u" "-" | trim "-"'
    ], 
    null, 
    FilteredWrapper::default()->withFilters([
        'replace' => new Filter('preg_replace', ['//', '', '$$'])
    ])
);
```
