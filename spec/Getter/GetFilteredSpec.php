<?php

namespace spec\DataMap\Getter;

use DataMap\Filter\Filter;
use DataMap\Input\ArrayInput;
use DataMap\Input\Input;
use PhpSpec\ObjectBehavior;
use spec\DataMap\Stub\StringObject;

final class GetFilteredSpec extends ObjectBehavior
{
    function it_gets_value_by_key_and_transforms_it_with_provided_callbacks(Input $input)
    {
        $this->beConstructedWith('get_by_key', 'trim', 'strtoupper');
        $input->get('get_by_key')->willReturn(' this is example ');

        $this($input)->shouldReturn('THIS IS EXAMPLE');
    }

    function it_can_make_use_of_Filter_definition(Input $input)
    {
        $this->beConstructedWith('key', 'trim', 'strtoupper', new Filter('explode', [' ', '$$']));
        $input->get('key')->willReturn(' this is example ');

        $this($input)->shouldReturn(['THIS', 'IS', 'EXAMPLE']);
    }

    function it_has_builtin_string_filter()
    {
        $this->beConstructedThrough('from', ['key']);
        $getString = $this->string();

        $getString(new ArrayInput(['key' => 123]))->shouldReturn('123');
        $getString(new ArrayInput(['key' => []]))->shouldReturn(null);

        $stringable = new StringObject('I`m stringable!');

        $getString(new ArrayInput(['key' => $stringable]))->shouldReturn('I`m stringable!');
    }

    function it_has_builtin_int_filter()
    {
        $this->beConstructedThrough('from', ['key']);
        $getInt = $this->int();

        $getInt(new ArrayInput(['key' => '123']))->shouldReturn(123);
        $getInt(new ArrayInput(['key' => 123.999]))->shouldReturn(123);
        $getInt(new ArrayInput(['key' => []]))->shouldReturn(null);
        $getInt(new ArrayInput(['key' => 'a123']))->shouldReturn(null);

        $stringable = new StringObject('123');

        $getInt(new ArrayInput(['key' => $stringable]))->shouldReturn(123);
    }

    function it_has_builtin_float_filter()
    {
        $this->beConstructedThrough('from', ['key']);
        $getFloat = $this->float();

        $getFloat(new ArrayInput(['key' => '123']))->shouldReturn(123.0);
        $getFloat(new ArrayInput(['key' => 123.999]))->shouldReturn(123.999);
        $getFloat(new ArrayInput(['key' => []]))->shouldReturn(null);
        $getFloat(new ArrayInput(['key' => 'a123']))->shouldReturn(null);

        $stringable = new StringObject('123.1');

        $getFloat(new ArrayInput(['key' => $stringable]))->shouldReturn(123.1);
    }

    function it_has_builtin_boolean_filter()
    {
        $this->beConstructedThrough('from', ['key']);
        $getBool = $this->bool();

        $getBool(new ArrayInput(['key' => '1']))->shouldReturn(true);
        $getBool(new ArrayInput(['key' => 1]))->shouldReturn(true);
        $getBool(new ArrayInput(['key' => '0']))->shouldReturn(false);
        $getBool(new ArrayInput(['key' => 0]))->shouldReturn(false);

        $getBool(new ArrayInput(['key' => '11']))->shouldReturn(null);
        $getBool(new ArrayInput(['key' => '00']))->shouldReturn(null);
        $getBool(new ArrayInput(['key' => 'x']))->shouldReturn(null);
        $getBool(new ArrayInput(['key' => []]))->shouldReturn(null);
    }

    function it_has_builtin_array_filter()
    {
        $this->beConstructedThrough('from', ['key']);
        $getArray = $this->array();

        $getArray(new ArrayInput(['key' => ['1']]))->shouldReturn(['1']);
        $getArray(new ArrayInput(['key' => new \ArrayObject(['1'])]))->shouldReturn(['1']);

        $iterate = static function (): iterable {
            yield 1;
            yield 2;
        };

        $getArray(new ArrayInput(['key' => $iterate()]))->shouldReturn([1, 2]);

        $getArray(new ArrayInput(['key' => '0']))->shouldReturn(null);
        $getArray(new ArrayInput(['key' => 0]))->shouldReturn(null);

        $o = new \stdClass();
        $o->one = 1;
        $o->two = 2;
        $getArray(new ArrayInput(['key' => $o]))->shouldReturn(null);
    }

    function it_has_builtin_string_explode_filter()
    {
        $this->beConstructedThrough('from', ['key']);
        $byDash = $this->explode('-');

        $byDash(new ArrayInput(['key' => 'i-like-kebab']))->shouldReturn(['i', 'like', 'kebab']);
        $byDash(new ArrayInput(['key' => 'have no dash']))->shouldReturn(['have no dash']);
    }

    function it_fails_to_explode_by_empty_string()
    {
        $this->beConstructedThrough('from', ['key']);
        $this->shouldThrow()->during('explode', ['']);
    }

    function it_has_builtin_string_implode_filter()
    {
        $this->beConstructedThrough('from', ['key']);
        $byDash = $this->implode('-');

        $byDash(new ArrayInput(['key' => ['i', 'like', 'kebab']]))->shouldReturn('i-like-kebab');
    }

    function it_has_builtin_string_upper_filter()
    {
        $this->beConstructedThrough('from', ['key']);
        $upper = $this->string()->upper();

        $upper(new ArrayInput(['key' => 'hello world']))->shouldReturn('HELLO WORLD');
        $upper(new ArrayInput(['key' => new StringObject('hello world')]))->shouldReturn('HELLO WORLD');
        $upper(new ArrayInput(['key' => null]))->shouldReturn(null);
    }

    function it_has_builtin_string_lower_filter()
    {
        $this->beConstructedThrough('from', ['key']);
        $lower = $this->string()->lower();

        $lower(new ArrayInput(['key' => 'HELLO WORLD']))->shouldReturn('hello world');
        $lower(new ArrayInput(['key' => new StringObject('HELLO WORLD')]))->shouldReturn('hello world');
        $lower(new ArrayInput(['key' => null]))->shouldReturn(null);
    }

    function it_has_builtin_string_trim_filter()
    {
        $this->beConstructedThrough('from', ['key']);
        $trim = $this->string()->trim();

        $trim(new ArrayInput(['key' => "\t\nhello world  "]))->shouldReturn('hello world');
        $trim(new ArrayInput(['key' => new StringObject("   hello world  \n\n")]))->shouldReturn('hello world');
        $trim(new ArrayInput(['key' => null]))->shouldReturn(null);
    }

    function it_has_builtin_string_format_filter()
    {
        $this->beConstructedThrough('from', ['key']);
        $format = $this->string()->format('Hello %s!!!');

        $format(new ArrayInput(['key' => 'John']))->shouldReturn('Hello John!!!');
        $format(new ArrayInput(['key' => new StringObject('Mary')]))->shouldReturn('Hello Mary!!!');
        $format(new ArrayInput(['key' => null]))->shouldReturn(null);
    }

    function it_has_builtin_string_replace_filter()
    {
        $this->beConstructedThrough('from', ['key']);
        $replace = $this->string()->replace('hello', 'hi');

        $replace(new ArrayInput(['key' => 'hello world, hello universe']))->shouldReturn('hi world, hi universe');
        $replace(new ArrayInput(['key' => new StringObject('hello world, hello universe')]))
            ->shouldReturn('hi world, hi universe');
        $replace(new ArrayInput(['key' => null]))->shouldReturn(null);
    }

    function it_has_builtin_strip_tags_filter()
    {
        $this->beConstructedThrough('from', ['key']);
        $stripTags = $this->string()->stripTags();

        $stripTags(new ArrayInput(['key' => 'I am <strong>strong</strong>.']))->shouldReturn('I am strong.');
        $stripTags(new ArrayInput(['key' => new StringObject('I am <strong>strong</strong>.')]))
            ->shouldReturn('I am strong.');
        $stripTags(new ArrayInput(['key' => null]))->shouldReturn(null);
    }

    function it_has_builtin_number_format_filter()
    {
        $this->beConstructedThrough('from', ['key']);
        $format = $this->float()->numberFormat(2, ',', ' ');

        $format(new ArrayInput(['key' => '1234.123']))->shouldReturn('1 234,12');
        $format(new ArrayInput(['key' => 1234.123]))->shouldReturn('1 234,12');
        $format(new ArrayInput(['key' => 1234]))->shouldReturn('1 234,00');
        $format(new ArrayInput(['key' => new StringObject('NaN')]))->shouldReturn(null);
        $format(new ArrayInput(['key' => null]))->shouldReturn(null);
    }

    function it_has_builtin_number_round_filter()
    {
        $this->beConstructedThrough('from', ['key']);
        $toFloat = $this->float()->round();
        $toFloat2 = $this->float()->round(2);
        $toInt = $this->float()->round()->int();

        $stringFloat = new ArrayInput(['key' => '1234.123']);
        $toFloat($stringFloat)->shouldReturn(1234.0);
        $toFloat2($stringFloat)->shouldReturn(1234.12);
        $toInt($stringFloat)->shouldReturn(1234);

        $float = new ArrayInput(['key' => 1234.123]);
        $toFloat($float)->shouldReturn(1234.0);
        $toFloat2($float)->shouldReturn(1234.12);
        $toInt($float)->shouldReturn(1234);

        $toFloat(new ArrayInput(['key' => new StringObject('NaN')]))->shouldReturn(null);
        $toFloat(new ArrayInput(['key' => null]))->shouldReturn(null);
    }

    function it_has_builtin_number_floor_filter()
    {
        $this->beConstructedThrough('from', ['key']);
        $toFloat = $this->float()->floor();

        $stringFloat = new ArrayInput(['key' => '1234.123']);
        $toFloat($stringFloat)->shouldReturn(1234.0);

        $float = new ArrayInput(['key' => 1234.123]);
        $toFloat($float)->shouldReturn(1234.0);

        $toFloat(new ArrayInput(['key' => new StringObject('NaN')]))->shouldReturn(null);
        $toFloat(new ArrayInput(['key' => null]))->shouldReturn(null);
    }

    function it_has_builtin_number_ceil_filter()
    {
        $this->beConstructedThrough('from', ['key']);
        $toFloat = $this->float()->ceil();
        $toInt = $this->float()->ceil()->int();

        $stringFloat = new ArrayInput(['key' => '1234.123']);
        $toFloat($stringFloat)->shouldReturn(1235.0);
        $toInt($stringFloat)->shouldReturn(1235);

        $float = new ArrayInput(['key' => 1234.123]);
        $toFloat($float)->shouldReturn(1235.0);
        $toInt($float)->shouldReturn(1235);

        $toFloat(new ArrayInput(['key' => new StringObject('NaN')]))->shouldReturn(null);
        $toFloat(new ArrayInput(['key' => null]))->shouldReturn(null);
    }

    function it_has_builtin_datetime_filter()
    {
        $this->beConstructedThrough('from', ['key']);
        $date = $this->date();

        $date(new ArrayInput(['key' => '2006-01-02 15:04']))
            ->shouldBeLike(new \DateTimeImmutable('2006-01-02 15:04'));

        $date(new ArrayInput(['key' => new \DateTime('2006-01-02 15:04')]))
            ->shouldBeLike(new \DateTimeImmutable('2006-01-02 15:04'));

        $date(new ArrayInput(['key' => null]))->shouldReturn(null);
        $date(new ArrayInput(['key' => 'not a date']))->shouldReturn(null);
    }

    function it_has_builtin_datetime_format_filter()
    {
        $this->beConstructedThrough('from', ['key']);

        $this->dateFormat('dmY H i s')
            ->__invoke(new ArrayInput(['key' => '2006-01-02 15:04']))
            ->shouldReturn('02012006 15 04 00');

        $this->dateFormat('dmY')
            ->__invoke(new ArrayInput(['key' => new \DateTime('2006-01-02 15:04')]))
            ->shouldReturn('02012006');

        $format = $this->dateFormat('dmY');

        $format(new ArrayInput(['key' => null]))->shouldReturn(null);
        $format(new ArrayInput(['key' => 'not a date']))->shouldReturn(null);
    }

    function it_has_builtin_count_filter()
    {
        $this->beConstructedThrough('from', ['key']);
        $count = $this->count();

        $count(new ArrayInput(['key' => [1, 2, 3]]))->shouldReturn(3);

        $countable = new class implements \Countable {
            public function count()
            {
                return 123;
            }
        };

        $count(new ArrayInput(['key' => $countable]))->shouldReturn(123);

        $count(new ArrayInput(['key' => null]))->shouldReturn(null);
        $count(new ArrayInput(['key' => 'not countable']))->shouldReturn(null);
    }

    function it_allows_to_set_default_value_if_output_is_null()
    {
        $this->beConstructedThrough('from', ['key']);
        $getDefault = $this->ifNull(123);

        $getDefault(new ArrayInput(['key' => null]))->shouldReturn(123);
    }

    function it_allows_to_set_default_value_if_key_not_found()
    {
        $this->beConstructedThrough('from', ['key']);
        $getDefault = $this->ifNull(123);

        $getDefault(new ArrayInput([]))->shouldReturn(123);
    }

    function it_allows_to_set_default_value_when_filter_result_is_null()
    {
        $this->beConstructedThrough('from', ['key']);
        $getDefault = $this->int()->ifNull(123);

        $getDefault(new ArrayInput(['key' => 'string']))->shouldReturn(123);
        $getDefault(new ArrayInput(['key' => 100]))->shouldReturn(100);
    }

    function it_allows_to_set_default_value_if_output_is_empty()
    {
        $this->beConstructedThrough('from', ['key']);
        $getDefault = $this->ifEmpty(123);

        $getDefault(new ArrayInput(['key' => 0]))->shouldReturn(123);
        $getDefault(new ArrayInput(['key' => 'not empty']))->shouldReturn('not empty');
    }

    function it_allows_to_set_default_value_when_filter_result_is_empty()
    {
        $this->beConstructedThrough('from', ['key']);
        $getDefault = $this->int()->ifEmpty(123);

        $getDefault(new ArrayInput(['key' => '0']))->shouldReturn(123);
    }

    function it_allows_to_add_custom_Closure_as_filter()
    {
        $this->beConstructedThrough('from', ['name']);
        $greeting = function (string $name): string {
            return "Hello {$name}!";
        };

        $get = $this->string()->with($greeting);

        $get(new ArrayInput(['name' => 'John']))->shouldReturn('Hello John!');
        $get(new ArrayInput([]))->shouldReturn(null);
    }
}
