<?php

namespace spec\DataMap\Input;

use DataMap\Exception\FailedToWrapInput;
use DataMap\Input\ArrayInput;
use PhpSpec\ObjectBehavior;

final class ArrayWrapperSpec extends ObjectBehavior
{
    function it_supports_array_and_ArrayAccess_types()
    {
        $this->supportedTypes()->shouldReturn(['array', \ArrayAccess::class]);
    }

    function it_wraps_array_with_ArrayInput()
    {
        $this->wrap(['data' => 'value'])->shouldBeLike(new ArrayInput(['data' => 'value']));
    }

    function it_wraps_ArrayAccess_with_ArrayInput()
    {
        $this
            ->wrap(new \ArrayObject(['data' => 'value']))
            ->shouldBeLike(new ArrayInput(new \ArrayObject(['data' => 'value'])));
    }

    function it_throws_FailedToWrapInput_when_data_cannot_be_wrapped()
    {
        $this->shouldThrow(FailedToWrapInput::class)->during('wrap', [new \stdClass()]);
    }
}
