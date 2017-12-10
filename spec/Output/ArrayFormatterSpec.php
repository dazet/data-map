<?php

namespace spec\DataMap\Output;

use PhpSpec\ObjectBehavior;

final class ArrayFormatterSpec extends ObjectBehavior
{
    function it_just_returns_result_array()
    {
        $this->beConstructedThrough('default');

        $output = ['one' => 1, 'two' => 2, 'three' => 3];
        $this->format($output)->shouldEqual($output);
    }
}
