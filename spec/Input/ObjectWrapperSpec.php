<?php

namespace spec\DataMap\Input;

use DataMap\Exception\FailedToWrapInput;
use DataMap\Input\ObjectInput;
use PhpSpec\ObjectBehavior;

final class ObjectWrapperSpec extends ObjectBehavior
{
    function it_supports_object_type()
    {
        $this->supportedTypes()->shouldBe(['object']);
    }

    function it_wraps_object_with_ObjectInput()
    {
        $object = new \stdClass();

        $this->wrap($object)->shouldBeLike(new ObjectInput($object));
    }

    function it_throws_FailedToWrapInput_when_data_is_not_object()
    {
        $this->shouldThrow(FailedToWrapInput::class)->during('wrap', [[]]);
    }
}
