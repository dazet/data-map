<?php

namespace spec\DataMap\Getter;

use DataMap\Input\ArrayInput;
use PhpSpec\ObjectBehavior;

final class GetJoinedStringsSpec extends ObjectBehavior
{
    function it_joins_strings_from_input_into_one_string()
    {
        $this->beConstructedWith(' / ', 'one', 'two', 'three');

        $this(new ArrayInput(['one' => '1', 'two' => '2', 'three' => '3']))->shouldBe('1 / 2 / 3');
    }

    function it_skips_empty_or_invalid_values_from_input()
    {
        $this->beConstructedWith(' / ', 'one', 'two', 'three', 'four');

        $this(new ArrayInput(['one' => '1', 'two' => ['2'], 'three' => null, 'four' => '4']))->shouldBe('1 / 4');
    }
}
