<?php

namespace spec\DataMap\Filter;

use DataMap\Filter\Filter;
use PhpSpec\ObjectBehavior;

final class FilterChainSpec extends ObjectBehavior
{
    function it_defines_getter_key_and_value_transformation_as_stream_of_filters()
    {
        $this->beConstructedWith('getter_key', new Filter('trim'), new Filter('strtoupper'));

        $this->key()->shouldBe('getter_key');
        $this->filter('    hello world    ')->shouldReturn('HELLO WORLD');
    }
}
