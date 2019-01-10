<?php

namespace spec\DataMap\Filter;

use DataMap\Exception\FailedToParseGetter;
use PhpSpec\ObjectBehavior;
use spec\DataMap\StringObject;

final class FilterChainParserSpec extends ObjectBehavior
{
    function it_has_default_string_cast()
    {
        $this->beConstructedThrough('default');
        $filter = $this->parse('key | string');

        $filter->key()->shouldReturn('key');
        $filter->filter(1)->shouldReturn('1');
        $filter->filter(0)->shouldReturn('0');
        $filter->filter(new StringObject('hello world'))->shouldReturn('hello world');

        $filter->filter(null)->shouldReturn(null);
        $filter->filter(true)->shouldReturn('1');
        $filter->filter(false)->shouldReturn('0');
        $filter->filter([])->shouldReturn(null);
    }

    function it_has_default_integer_cast()
    {
        $this->beConstructedThrough('default');
        $int = $this->parse('key | int');
        $integer = $this->parse('key | integer');

        $int->filter('123')->shouldReturn(123);
        $integer->filter('123')->shouldReturn(123);

        $int->filter('123.997')->shouldReturn(123);
        $integer->filter('123.997')->shouldReturn(123);

        $int->filter('0')->shouldReturn(0);
        $int->filter('1')->shouldReturn(1);

        $int->filter(false)->shouldReturn(0);
        $int->filter(true)->shouldReturn(1);

        $int->filter('')->shouldReturn(null);
        $int->filter('abc')->shouldReturn(null);
        $int->filter([])->shouldReturn(null);
    }

    function it_has_default_float_cast()
    {
        $this->beConstructedThrough('default');
        $float = $this->parse('key | float');

        $float->filter('123.456')->shouldReturn(123.456);
        $float->filter('123')->shouldReturn(123.0);
        $float->filter(123)->shouldReturn(123.0);
        $float->filter(true)->shouldReturn(1.0);
        $float->filter(false)->shouldReturn(0.0);
        $float->filter(null)->shouldReturn(null);
        $float->filter('')->shouldReturn(null);
        $float->filter('abc')->shouldReturn(null);
        $float->filter([])->shouldReturn(null);
    }

    function it_has_default_boolean_cast()
    {
        $this->beConstructedThrough('default');
        $bool = $this->parse('key | bool');
        $boolean = $this->parse('key | boolean');

        $bool->filter(1)->shouldReturn(true);
        $boolean->filter(1)->shouldReturn(true);
        $bool->filter('1')->shouldReturn(true);
        $boolean->filter('1')->shouldReturn(true);
        $bool->filter('true')->shouldReturn(true);
        $boolean->filter('true')->shouldReturn(true);

        $bool->filter(0)->shouldReturn(false);
        $boolean->filter(0)->shouldReturn(false);
        $bool->filter('0')->shouldReturn(false);
        $boolean->filter('0')->shouldReturn(false);
        $bool->filter('false')->shouldReturn(false);
        $boolean->filter('false')->shouldReturn(false);

        $bool->filter('')->shouldReturn(null);
        $bool->filter('abc')->shouldReturn(null);
        $bool->filter([])->shouldReturn(null);
    }

    function it_has_default_array_cast_filter()
    {
        $this->beConstructedThrough('default');

        $array = $this->parse('key | array');
        $array->filter([1, 2, 3])->shouldReturn([1, 2, 3]);
        $array->filter(new \ArrayObject([1, 2, 3]))->shouldReturn([1, 2, 3]);

        $iterable = function () {
            yield 1;
            yield 2;
            yield 3;
        };
        $array->filter($iterable())->shouldReturn([1, 2, 3]);

        $array->filter('string')->shouldReturn(null);
        $array->filter(new StringObject('string'))->shouldReturn(null);
        $array->filter(1)->shouldReturn(null);
        $array->filter(false)->shouldReturn(null);
        $array->filter(null)->shouldReturn(null);
    }

    function it_has_default_explode_filter()
    {
        $this->beConstructedThrough('default');
        $explode = $this->parse('key | explode');

        $explode->filter('text')->shouldReturn(['text']);
        $explode->filter('csv,with,commas')->shouldReturn(['csv', 'with', 'commas']);

        $this->parse('key | explode " "')->filter('text to explode')->shouldReturn(['text', 'to', 'explode']);
    }

    function it_has_default_implode_filter()
    {
        $this->beConstructedThrough('default');
        $implode = $this->parse('key | implode');

        $implode->filter(['text'])->shouldReturn('text');
        $implode->filter(['csv', 'with', 'commas'])->shouldReturn('csv,with,commas');

        $this->parse('key | implode " "')->filter(['text', 'to', 'implode'])->shouldReturn('text to implode');
        $this->parse('key | array | implode " "')
            ->filter(new \ArrayIterator(['one two three']))
            ->shouldReturn('one two three');
    }

    function it_has_default_upper_case_string_filter()
    {
        $this->beConstructedThrough('default');
        $upper = $this->parse('key | upper');

        $upper->filter('hello world!')->shouldReturn('HELLO WORLD!');
        $upper->filter('zażółć gęślą jaźń')->shouldReturn('ZAŻÓŁĆ GĘŚLĄ JAŹŃ');

        $this->parse('key | string | upper')->filter(new StringObject('hello world'))->shouldReturn('HELLO WORLD');

        $this->parse('key | string | upper')->filter(null)->shouldReturn(null);
        $this->parse('key | string | upper')->filter([])->shouldReturn(null);
    }

    function it_has_default_lower_case_string_filter()
    {
        $this->beConstructedThrough('default');
        $lower = $this->parse('key | lower');

        $lower->filter('HELLO WORLD!')->shouldReturn('hello world!');
        $lower->filter('ZAŻÓŁĆ GĘŚLĄ JAŹŃ')->shouldReturn('zażółć gęślą jaźń');

        $this->parse('key | string | lower')->filter(new StringObject('HELLO WORLD'))->shouldReturn('hello world');

        $this->parse('key | string | lower')->filter(null)->shouldReturn(null);
        $this->parse('key | string | lower')->filter([])->shouldReturn(null);
    }

    function it_has_default_trim_string_filters()
    {
        $this->beConstructedThrough('default');

        $this->parse('key | trim')->filter('   i like space   ')->shouldReturn('i like space');
        $this->parse('key | trim ". "')->filter(' ...and now some space  ')->shouldReturn('and now some space');

        $this->parse('key | ltrim')->filter('   i like space   ')->shouldReturn('i like space   ');
        $this->parse('key | ltrim ". "')->filter(' ...and now some space  ')->shouldReturn('and now some space  ');

        $this->parse('key | rtrim')->filter('   i like space   ')->shouldReturn('   i like space');
        $this->parse('key | rtrim ". "')->filter(' ...and now some space  ')->shouldReturn(' ...and now some space');
    }

    function it_has_default_string_format_filter()
    {
        $this->beConstructedThrough('default');
        $format = $this->parse('key | format "Hello %s, how are you?"');

        $format->filter('John')->shouldReturn('Hello John, how are you?');
        $format->filter('Mary')->shouldReturn('Hello Mary, how are you?');

        $this->parse('key | format "price: $%01.2f"')->filter(12.3499)->shouldReturn('price: $12.35');
    }

    function it_has_default_string_replace_filter()
    {
        $this->beConstructedThrough('default');
        $this->parse('key | replace red green')->filter('red light')->shouldReturn('green light');
        $this->parse('key | replace "red" "green"')->filter('red light')->shouldReturn('green light');
        $this->parse('key | replace "red"')->filter('red light')->shouldReturn(' light');
        $this->parse('key | replace')->filter('red light')->shouldReturn('red light');
    }

    function it_has_default_strip_tags_filter()
    {
        $this->beConstructedThrough('default');
        $this->parse('key | strip_tags')->filter('<h1>Title</h1>')->shouldReturn('Title');
        $this->parse('key | strip_tags')
            ->filter('<javascript>alert("boo");</javascript>')
            ->shouldReturn('alert("boo");');
    }

    function it_has_default_number_format_filter()
    {
        $this->beConstructedThrough('default');
        $this->parse('key | number_format')->filter(12300.4567)->shouldReturn('12300.46');
        $this->parse('key | number_format 3')->filter(12300.4567)->shouldReturn('12300.457');
        $this->parse('key | number_format 3 ","')->filter(12300.4567)->shouldReturn('12300,457');
        $this->parse('key | number_format _ ","')->filter(12300.4567)->shouldReturn('12300,46');
        $this->parse('key | number_format _ "," "."')->filter(12300.4567)->shouldReturn('12.300,46');
        $this->parse('key | number_format 2 . " "')->filter(12300.4567)->shouldReturn('12 300.46');
    }

    function it_has_default_number_round_filter()
    {
        $this->beConstructedThrough('default');
        $this->parse('key | round')->filter(123.4567)->shouldReturn(123.0);
        $this->parse('key | round')->filter(123)->shouldReturn(123.0);
        $this->parse('key | round 2')->filter(123.4567)->shouldReturn(123.46);
        $this->parse('key | float | round 2')->filter('123.4567')->shouldReturn(123.46);

        $this->parse('key | float | round')->filter('a')->shouldReturn(null);
        $this->parse('key | float | round')->filter(false)->shouldReturn(0.0);
        $this->parse('key | float | round')->filter(true)->shouldReturn(1.0);
    }

    function it_has_default_number_floor_filter()
    {
        $this->beConstructedThrough('default');
        $this->parse('key | floor')->filter(123.4567)->shouldReturn(123.0);
        $this->parse('key | floor')->filter(123)->shouldReturn(123.0);
        $this->parse('key | float | floor')->filter('123.4567')->shouldReturn(123.0);

        $this->parse('key | float | floor')->filter('a')->shouldReturn(null);
    }

    function it_has_default_number_ceil_filter()
    {
        $this->beConstructedThrough('default');
        $this->parse('key | ceil')->filter(123.4567)->shouldReturn(124.0);
        $this->parse('key | ceil')->filter(123)->shouldReturn(123.0);
        $this->parse('key | float | ceil')->filter('123.4567')->shouldReturn(124.0);

        $this->parse('key | float | ceil')->filter('a')->shouldReturn(null);
    }

    function it_has_default_datetime_filter_parsing_to_DateTimeImmutable()
    {
        $this->beConstructedThrough('default');
        $datetime = $this->parse('key | datetime');

        $datetime->filter('1999-12-31 23:59:59')->shouldBeLike(new \DateTimeImmutable('1999-12-31 23:59:59'));
        $datetime->filter('23:59:59')->shouldBeLike(new \DateTimeImmutable('today 23:59:59'));

        $datetime->filter('string')->shouldBe(null);
        $datetime->filter(true)->shouldBe(null);
        $datetime->filter(false)->shouldBe(null);
        $datetime->filter([])->shouldBe(null);
    }

    function it_has_default_date_formatting_filter()
    {
        $this->beConstructedThrough('default');
        $datetime = $this->parse('key | date_format "Y m d"');

        $datetime->filter('1999-12-31 23:59:59')->shouldReturn('1999 12 31');
        $datetime->filter(new \DateTimeImmutable('1999-12-31 23:59:59'))->shouldReturn('1999 12 31');
        $datetime->filter('12:00')->shouldReturn((new \DateTimeImmutable('today'))->format('Y m d'));

        $datetime->filter('string')->shouldBe(null);
        $datetime->filter(true)->shouldBe(null);
        $datetime->filter(false)->shouldBe(null);
        $datetime->filter([])->shouldBe(null);
    }

    function it_has_default_date_modifying_filter()
    {
        $this->beConstructedThrough('default');
        $datetime = $this->parse('key | date_modify "+1 day"');

        $datetime->filter('1999-12-31 23:59:59')->shouldBeLike(new \DateTimeImmutable('2000-01-01 23:59:59'));
        $datetime->filter(new \DateTimeImmutable('1999-12-31 23:59:59'))
            ->shouldBeLike(new \DateTimeImmutable('2000-01-01 23:59:59'));

        $datetime->filter('string')->shouldBe(null);
        $datetime->filter(true)->shouldBe(null);
        $datetime->filter(false)->shouldBe(null);
        $datetime->filter([])->shouldBe(null);

        $this->parse('key | date_modify')
            ->filter('1999-12-31 23:59:59')
            ->shouldBeLike(new \DateTimeImmutable('1999-12-31 23:59:59'));
    }

    function it_has_default_timestamp_filter()
    {
        $this->beConstructedThrough('default');
        $timestamp = $this->parse('key | timestamp');
        $date = new \DateTimeImmutable();
        $timestamp->filter($date)->shouldReturn($date->getTimestamp());
        $timestamp->filter($date->format('Y-m-d H:i:s'))->shouldReturn($date->getTimestamp());

        $timestamp->filter('string')->shouldBe(null);
        $timestamp->filter(true)->shouldBe(null);
        $timestamp->filter(false)->shouldBe(null);
        $timestamp->filter([])->shouldBe(null);
    }

    function it_has_default_json_encode_filter()
    {
        $this->beConstructedThrough('default');
        $json = $this->parse('key | json_encode');

        $json->filter(['key' => 'value'])->shouldReturn('{"key":"value"}');
    }

    function it_has_default_count_filter()
    {
        $this->beConstructedThrough('default');
        $count = $this->parse('key | count');

        $count->filter([])->shouldReturn(0);
        $count->filter(['one', 'two', 'three'])->shouldReturn(3);
        $count->filter(new \ArrayObject(['one', 'two', 'three']))->shouldReturn(3);

        $count->filter('string')->shouldReturn(null);
        $count->filter(new StringObject('string'))->shouldReturn(null);
        $count->filter(12)->shouldReturn(null);
        $count->filter(12.0)->shouldReturn(null);
        $count->filter(true)->shouldReturn(null);
        $count->filter(false)->shouldReturn(null);
        $count->filter(null)->shouldReturn(null);
    }

    function it_has_default_empty_value_replacement_filter()
    {
        $this->beConstructedThrough('default');
        $ifEmpty = $this->parse('key | if_empty "(empty)"');
        $ifEmpty->filter('')->shouldReturn('(empty)');
        $ifEmpty->filter(null)->shouldReturn('(empty)');
        $ifEmpty->filter(false)->shouldReturn('(empty)');
        $ifEmpty->filter([])->shouldReturn('(empty)');

        $ifEmpty->filter('a')->shouldReturn('a');
        $emptyObject = new StringObject('');
        $ifEmpty->filter($emptyObject)->shouldReturn($emptyObject);
        $ifEmpty->filter(true)->shouldReturn(true);
        $ifEmpty->filter(['a'])->shouldReturn(['a']);
    }

    function it_has_default_null_value_replacement_filter()
    {
        $this->beConstructedThrough('default');
        $ifEmpty = $this->parse('key | if_empty "(empty)"');
        $ifEmpty->filter('')->shouldReturn('(empty)');
        $ifEmpty->filter(null)->shouldReturn('(empty)');
        $ifEmpty->filter(false)->shouldReturn('(empty)');
        $ifEmpty->filter([])->shouldReturn('(empty)');
        $ifEmpty->filter(0)->shouldReturn('(empty)');

        $ifEmpty->filter('a')->shouldReturn('a');
        $emptyObject = new StringObject('');
        $ifEmpty->filter($emptyObject)->shouldReturn($emptyObject);
        $ifEmpty->filter(true)->shouldReturn(true);
        $ifEmpty->filter(['a'])->shouldReturn(['a']);
        $ifEmpty->filter(-1)->shouldReturn(-1);
        $ifEmpty->filter(1)->shouldReturn(1);

        $defaultIfEmpty = $this->parse('key | if_empty');
        $defaultIfEmpty->filter('')->shouldReturn(null);
        $defaultIfEmpty->filter(null)->shouldReturn(null);
        $defaultIfEmpty->filter(false)->shouldReturn(null);
        $defaultIfEmpty->filter([])->shouldReturn(null);
        $defaultIfEmpty->filter(0)->shouldReturn(null);
    }

    function it_allows_any_php_function_by_default()
    {
        $this->beConstructedThrough('default');

        $this->parse('key | string | md5')
            ->filter('hello world')
            ->shouldReturn(md5('hello world'));

        $this->parse('key | preg_replace "/\s+/" " " $$')
            ->filter('give    me    some    space')
            ->shouldReturn('give me some space');
    }

    function it_does_not_allow_any_php_function_in_safe_mode()
    {
        $this->beConstructedThrough('safeDefault');

        $this->shouldThrow(FailedToParseGetter::class)->during('parse', ['key | string | md5']);
    }
}
