<?php

namespace spec\DataMap\Filter;

use DataMap\Exception\FailedToParseGetter;
use PhpSpec\ObjectBehavior;
use Psr\Container\ContainerInterface;
use spec\DataMap\Stub\StringObject;
use function md5;

final class InputFilterParserSpec extends ObjectBehavior
{
    function it_has_default_string_cast()
    {
        $this->beConstructedThrough('default');
        $filter = $this->parse('key | string');

        $filter->key()->shouldReturn('key');
        $filter->transform(1)->shouldReturn('1');
        $filter->transform(0)->shouldReturn('0');
        $filter->transform(new StringObject('hello world'))->shouldReturn('hello world');

        $filter->transform(null)->shouldReturn(null);
        $filter->transform(true)->shouldReturn('1');
        $filter->transform(false)->shouldReturn('0');
        $filter->transform([])->shouldReturn(null);
    }

    function it_has_default_integer_cast()
    {
        $this->beConstructedThrough('default');
        $int = $this->parse('key | int');
        $integer = $this->parse('key | integer');

        $int->transform('123')->shouldReturn(123);
        $integer->transform('123')->shouldReturn(123);

        $int->transform('123.997')->shouldReturn(123);
        $integer->transform('123.997')->shouldReturn(123);

        $int->transform('0')->shouldReturn(0);
        $int->transform('1')->shouldReturn(1);

        $int->transform(false)->shouldReturn(0);
        $int->transform(true)->shouldReturn(1);

        $int->transform('')->shouldReturn(null);
        $int->transform('abc')->shouldReturn(null);
        $int->transform([])->shouldReturn(null);
    }

    function it_has_default_float_cast()
    {
        $this->beConstructedThrough('default');
        $float = $this->parse('key | float');

        $float->transform('123.456')->shouldReturn(123.456);
        $float->transform('123')->shouldReturn(123.0);
        $float->transform(123)->shouldReturn(123.0);
        $float->transform(true)->shouldReturn(1.0);
        $float->transform(false)->shouldReturn(0.0);
        $float->transform(null)->shouldReturn(null);
        $float->transform('')->shouldReturn(null);
        $float->transform('abc')->shouldReturn(null);
        $float->transform([])->shouldReturn(null);
    }

    function it_has_default_boolean_cast()
    {
        $this->beConstructedThrough('default');
        $bool = $this->parse('key | bool');
        $boolean = $this->parse('key | boolean');

        $tests = [
            [1, true],
            ['1', true],

            [0, false],
            ['0', false],

            ['true', null],
            ['false', null],
            ['', null],
            ['abc', null],
            [[], null],
        ];

        foreach ($tests as [$value, $expected]) {
            $bool->transform($value)->shouldReturn($expected);
            $boolean->transform($value)->shouldReturn($expected);
        }
    }

    function it_has_default_array_cast_filter()
    {
        $this->beConstructedThrough('default');

        $array = $this->parse('key | array');
        $array->transform([1, 2, 3])->shouldReturn([1, 2, 3]);
        $array->transform(new \ArrayObject([1, 2, 3]))->shouldReturn([1, 2, 3]);

        $iterable = function () {
            yield 1;
            yield 2;
            yield 3;
        };
        $array->transform($iterable())->shouldReturn([1, 2, 3]);

        $array->transform('string')->shouldReturn(null);
        $array->transform(new StringObject('string'))->shouldReturn(null);
        $array->transform(1)->shouldReturn(null);
        $array->transform(false)->shouldReturn(null);
        $array->transform(null)->shouldReturn(null);
    }

    function it_has_default_explode_filter()
    {
        $this->beConstructedThrough('default');
        $explode = $this->parse('key | explode');

        $explode->transform('text')->shouldReturn(['text']);
        $explode->transform('csv,with,commas')->shouldReturn(['csv', 'with', 'commas']);

        $this->parse('key | explode " "')->transform('text to explode')->shouldReturn(['text', 'to', 'explode']);
    }

    function it_has_default_implode_filter()
    {
        $this->beConstructedThrough('default');
        $implode = $this->parse('key | implode');

        $implode->transform(['text'])->shouldReturn('text');
        $implode->transform(['csv', 'with', 'commas'])->shouldReturn('csv,with,commas');

        $this->parse('key | implode " "')->transform(['text', 'to', 'implode'])->shouldReturn('text to implode');
        $this->parse('key | array | implode " "')
            ->transform(new \ArrayIterator(['one two three']))
            ->shouldReturn('one two three');
    }

    function it_has_default_upper_case_string_filter()
    {
        $this->beConstructedThrough('default');
        $upper = $this->parse('key | upper');

        $upper->transform('hello world!')->shouldReturn('HELLO WORLD!');
        $upper->transform('zażółć gęślą jaźń')->shouldReturn('ZAŻÓŁĆ GĘŚLĄ JAŹŃ');

        $this->parse('key | string | upper')->transform(new StringObject('hello world'))->shouldReturn('HELLO WORLD');

        $this->parse('key | string | upper')->transform(null)->shouldReturn(null);
        $this->parse('key | string | upper')->transform([])->shouldReturn(null);
    }

    function it_has_default_lower_case_string_filter()
    {
        $this->beConstructedThrough('default');
        $lower = $this->parse('key | lower');

        $lower->transform('HELLO WORLD!')->shouldReturn('hello world!');
        $lower->transform('ZAŻÓŁĆ GĘŚLĄ JAŹŃ')->shouldReturn('zażółć gęślą jaźń');

        $this->parse('key | string | lower')->transform(new StringObject('HELLO WORLD'))->shouldReturn('hello world');

        $this->parse('key | string | lower')->transform(null)->shouldReturn(null);
        $this->parse('key | string | lower')->transform([])->shouldReturn(null);
    }

    function it_has_default_trim_string_filters()
    {
        $this->beConstructedThrough('default');

        $this->parse('key | trim')->transform('   i like space   ')->shouldReturn('i like space');
        $this->parse('key | trim ". "')->transform(' ...and now some space  ')->shouldReturn('and now some space');

        $this->parse('key | ltrim')->transform('   i like space   ')->shouldReturn('i like space   ');
        $this->parse('key | ltrim ". "')->transform(' ...and now some space  ')->shouldReturn('and now some space  ');

        $this->parse('key | rtrim')->transform('   i like space   ')->shouldReturn('   i like space');
        $this->parse('key | rtrim ". "')->transform(' ...and now some space  ')->shouldReturn(' ...and now some space');
    }

    function it_has_default_string_format_filter()
    {
        $this->beConstructedThrough('default');
        $format = $this->parse('key | format "Hello %s, how are you?"');

        $format->transform('John')->shouldReturn('Hello John, how are you?');
        $format->transform('Mary')->shouldReturn('Hello Mary, how are you?');

        $this->parse('key | format "price: $%01.2f"')->transform(12.3499)->shouldReturn('price: $12.35');
    }

    function it_has_default_string_replace_filter()
    {
        $this->beConstructedThrough('default');
        $this->parse('key | replace red green')->transform('red light')->shouldReturn('green light');
        $this->parse('key | replace "red" "green"')->transform('red light')->shouldReturn('green light');
        $this->parse('key | replace "red"')->transform('red light')->shouldReturn(' light');
        $this->parse('key | replace')->transform('red light')->shouldReturn('red light');
    }

    function it_has_default_strip_tags_filter()
    {
        $this->beConstructedThrough('default');
        $this->parse('key | strip_tags')->transform('<h1>Title</h1>')->shouldReturn('Title');
        $this->parse('key | strip_tags')
            ->transform('<javascript>alert("boo");</javascript>')
            ->shouldReturn('alert("boo");');
    }

    function it_has_default_number_format_filter()
    {
        $this->beConstructedThrough('default');
        $this->parse('key | number_format')->transform(12300.4567)->shouldReturn('12300.46');
        $this->parse('key | number_format 3')->transform(12300.4567)->shouldReturn('12300.457');
        $this->parse('key | number_format 3 ","')->transform(12300.4567)->shouldReturn('12300,457');
        $this->parse('key | number_format _ ","')->transform(12300.4567)->shouldReturn('12300,46');
        $this->parse('key | number_format _ "," "."')->transform(12300.4567)->shouldReturn('12.300,46');
        $this->parse('key | number_format 2 . " "')->transform(12300.4567)->shouldReturn('12 300.46');
    }

    function it_has_default_number_round_filter()
    {
        $this->beConstructedThrough('default');
        $this->parse('key | round')->transform(123.4567)->shouldReturn(123.0);
        $this->parse('key | round')->transform(123)->shouldReturn(123.0);
        $this->parse('key | round 2')->transform(123.4567)->shouldReturn(123.46);
        $this->parse('key | float | round 2')->transform('123.4567')->shouldReturn(123.46);

        $this->parse('key | float | round')->transform('a')->shouldReturn(null);
        $this->parse('key | float | round')->transform(false)->shouldReturn(0.0);
        $this->parse('key | float | round')->transform(true)->shouldReturn(1.0);
    }

    function it_has_default_number_floor_filter()
    {
        $this->beConstructedThrough('default');
        $this->parse('key | floor')->transform(123.4567)->shouldReturn(123.0);
        $this->parse('key | floor')->transform(123)->shouldReturn(123.0);
        $this->parse('key | float | floor')->transform('123.4567')->shouldReturn(123.0);

        $this->parse('key | float | floor')->transform('a')->shouldReturn(null);
    }

    function it_has_default_number_ceil_filter()
    {
        $this->beConstructedThrough('default');
        $this->parse('key | ceil')->transform(123.4567)->shouldReturn(124.0);
        $this->parse('key | ceil')->transform(123)->shouldReturn(123.0);
        $this->parse('key | float | ceil')->transform('123.4567')->shouldReturn(124.0);

        $this->parse('key | float | ceil')->transform('a')->shouldReturn(null);
    }

    function it_has_default_datetime_filter_parsing_to_DateTimeImmutable()
    {
        $this->beConstructedThrough('default');
        $datetime = $this->parse('key | datetime');

        $datetime->transform('1999-12-31 23:59:59')->shouldBeLike(new \DateTimeImmutable('1999-12-31 23:59:59'));
        $datetime->transform('23:59:59')->shouldBeLike(new \DateTimeImmutable('today 23:59:59'));

        $datetime->transform('string')->shouldBe(null);
        $datetime->transform(true)->shouldBe(null);
        $datetime->transform(false)->shouldBe(null);
        $datetime->transform([])->shouldBe(null);
    }

    function it_has_default_date_formatting_filter()
    {
        $this->beConstructedThrough('default');
        $datetime = $this->parse('key | date_format "Y m d"');

        $datetime->transform('1999-12-31 23:59:59')->shouldReturn('1999 12 31');
        $datetime->transform(new \DateTimeImmutable('1999-12-31 23:59:59'))->shouldReturn('1999 12 31');
        $datetime->transform('12:00')->shouldReturn((new \DateTimeImmutable('today'))->format('Y m d'));

        $datetime->transform('string')->shouldBe(null);
        $datetime->transform(true)->shouldBe(null);
        $datetime->transform(false)->shouldBe(null);
        $datetime->transform([])->shouldBe(null);
    }

    function it_has_default_date_modifying_filter()
    {
        $this->beConstructedThrough('default');
        $datetime = $this->parse('key | date_modify "+1 day"');

        $datetime->transform('1999-12-31 23:59:59')->shouldBeLike(new \DateTimeImmutable('2000-01-01 23:59:59'));
        $datetime->transform(new \DateTimeImmutable('1999-12-31 23:59:59'))
            ->shouldBeLike(new \DateTimeImmutable('2000-01-01 23:59:59'));

        $datetime->transform('string')->shouldBe(null);
        $datetime->transform(true)->shouldBe(null);
        $datetime->transform(false)->shouldBe(null);
        $datetime->transform([])->shouldBe(null);

        $this->parse('key | date_modify')
            ->transform('1999-12-31 23:59:59')
            ->shouldBeLike(new \DateTimeImmutable('1999-12-31 23:59:59'));
    }

    function it_has_default_timestamp_filter()
    {
        $this->beConstructedThrough('default');
        $timestamp = $this->parse('key | timestamp');
        $date = new \DateTimeImmutable();
        $timestamp->transform($date)->shouldReturn($date->getTimestamp());
        $timestamp->transform($date->format('Y-m-d H:i:s'))->shouldReturn($date->getTimestamp());

        $timestamp->transform('string')->shouldBe(null);
        $timestamp->transform(true)->shouldBe(null);
        $timestamp->transform(false)->shouldBe(null);
        $timestamp->transform([])->shouldBe(null);
    }

    function it_has_default_json_encode_filter()
    {
        $this->beConstructedThrough('default');
        $json = $this->parse('key | json_encode');

        $json->transform(['key' => 'value'])->shouldReturn('{"key":"value"}');
    }

    function it_has_default_count_filter()
    {
        $this->beConstructedThrough('default');
        $count = $this->parse('key | count');

        $count->transform([])->shouldReturn(0);
        $count->transform(['one', 'two', 'three'])->shouldReturn(3);
        $count->transform(new \ArrayObject(['one', 'two', 'three']))->shouldReturn(3);

        $count->transform('string')->shouldReturn(null);
        $count->transform(new StringObject('string'))->shouldReturn(null);
        $count->transform(12)->shouldReturn(null);
        $count->transform(12.0)->shouldReturn(null);
        $count->transform(true)->shouldReturn(null);
        $count->transform(false)->shouldReturn(null);
        $count->transform(null)->shouldReturn(null);
    }

    function it_has_default_empty_value_replacement_filter()
    {
        $this->beConstructedThrough('default');
        $ifEmpty = $this->parse('key | if_empty "(empty)"');
        $ifEmpty->transform('')->shouldReturn('(empty)');
        $ifEmpty->transform(null)->shouldReturn('(empty)');
        $ifEmpty->transform(false)->shouldReturn('(empty)');
        $ifEmpty->transform([])->shouldReturn('(empty)');

        $ifEmpty->transform('a')->shouldReturn('a');
        $emptyObject = new StringObject('');
        $ifEmpty->transform($emptyObject)->shouldReturn($emptyObject);
        $ifEmpty->transform(true)->shouldReturn(true);
        $ifEmpty->transform(['a'])->shouldReturn(['a']);
    }

    function it_has_default_null_value_replacement_filter()
    {
        $this->beConstructedThrough('default');
        $ifEmpty = $this->parse('key | if_empty "(empty)"');
        $ifEmpty->transform('')->shouldReturn('(empty)');
        $ifEmpty->transform(null)->shouldReturn('(empty)');
        $ifEmpty->transform(false)->shouldReturn('(empty)');
        $ifEmpty->transform([])->shouldReturn('(empty)');
        $ifEmpty->transform(0)->shouldReturn('(empty)');

        $ifEmpty->transform('a')->shouldReturn('a');
        $emptyObject = new StringObject('');
        $ifEmpty->transform($emptyObject)->shouldReturn($emptyObject);
        $ifEmpty->transform(true)->shouldReturn(true);
        $ifEmpty->transform(['a'])->shouldReturn(['a']);
        $ifEmpty->transform(-1)->shouldReturn(-1);
        $ifEmpty->transform(1)->shouldReturn(1);

        $defaultIfEmpty = $this->parse('key | if_empty');
        $defaultIfEmpty->transform('')->shouldReturn(null);
        $defaultIfEmpty->transform(null)->shouldReturn(null);
        $defaultIfEmpty->transform(false)->shouldReturn(null);
        $defaultIfEmpty->transform([])->shouldReturn(null);
        $defaultIfEmpty->transform(0)->shouldReturn(null);
    }

    function it_allows_any_php_function_by_default()
    {
        $this->beConstructedThrough('default');

        $this->parse('key | string | md5')
            ->transform('hello world')
            ->shouldReturn(md5('hello world'));

        $this->parse('key | preg_replace "/\s+/" " " $$')
            ->transform('give    me    some    space')
            ->shouldReturn('give me some space');
    }

    function it_does_not_allow_any_php_function_in_safe_mode()
    {
        $this->beConstructedThrough('safeDefault');

        $this->shouldThrow(FailedToParseGetter::class)->during('parse', ['key | string | md5']);
    }

    function it_throws_FailedToParseGetter_when_getter_key_is_empty()
    {
        $this->beConstructedThrough('default');

        $this->shouldThrow(FailedToParseGetter::class)->during('parse', [' | string']);
    }

    function it_allows_to_use_pipe_char_when_filter_definition_enclosed_in_backticks()
    {
        $this->beConstructedThrough('default');

        $this->parse('key |` trim | `| upper')
            ->transform('|hello|')
            ->shouldReturn('HELLO');
    }

    function it_can_be_created_with_custom_filter_registry()
    {
        $registry = new DummyFilterContainer('my_filter', 'my_filter_2');

        $this->beConstructedWith([], false, $registry);

        $this->parse('key | my_filter')->transform('hello')->shouldReturn('hello filtered by my_filter');
        $this->parse('key | my_filter_2')->transform('hello')->shouldReturn('hello filtered by my_filter_2');
    }

    function it_throws_FailedToParseGetter_when_filter_not_defined_in_registry()
    {
        $registry = new DummyFilterContainer('my_filter', 'my_filter_2');

        $this->beConstructedWith([], false, $registry);
        $this->shouldThrow(FailedToParseGetter::class)->during('parse', ['key | something_else']);
    }

    function it_throws_FailedToParseGetter_when_container_returns_something_other_than_Filter()
    {
        $registry = new class implements ContainerInterface {
            public function get($id)
            {
                return $id;
            }

            public function has($id): bool
            {
                return true;
            }
        };

        $this->beConstructedWith([], false, $registry);
        $this->shouldThrow(FailedToParseGetter::class)->during('parse', ['key | my_filter']);
    }
}
