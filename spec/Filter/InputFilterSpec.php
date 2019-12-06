<?php

namespace spec\DataMap\Filter;

use DataMap\Filter\Filter;
use PhpSpec\ObjectBehavior;

final class InputFilterSpec extends ObjectBehavior
{
    function it_defines_getter_key_and_series_of_filers_forming_value()
    {
        $this->beConstructedWith('getter_key', new Filter('trim'), new Filter('strtoupper'));

        $this->key()->shouldBe('getter_key');
        $this->transform('    hello world    ')->shouldReturn('HELLO WORLD');
    }
}
