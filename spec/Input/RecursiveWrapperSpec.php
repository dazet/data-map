<?php

namespace spec\DataMap\Input;

use DataMap\Input\ExtensibleWrapper;
use DataMap\Input\Input;
use DataMap\Input\RecursiveInput;
use PhpSpec\ObjectBehavior;

final class RecursiveWrapperSpec extends ObjectBehavior
{
    function it_supports_types_supported_by_inner_wrapper(ExtensibleWrapper $inner)
    {
        $this->beConstructedWith($inner);

        $inner->supportedTypes()->willReturn(['array', 'string']);

        $this->supportedTypes()->shouldBe(['array', 'string']);
    }

    function it_wraps_data_through_inner_wrapper_and_decorates_with_RecursiveInput(
        ExtensibleWrapper $inner,
        Input $innerInput
    ) {
        $this->beConstructedWith($inner);

        $data = new \stdClass();
        $inner->wrap($data)->willReturn($innerInput);

        $this->wrap($data)
            ->shouldBeLike(new RecursiveInput($innerInput->getWrappedObject(), $this->getWrappedObject()));
    }
}
