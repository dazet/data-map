<?php

namespace spec\DataMap\Input;

use DataMap\Exception\FailedToWrapInput;
use DataMap\Input\ArrayWrapper;
use DataMap\Input\ExtensibleWrapper;
use DataMap\Input\Input;
use DataMap\Input\ObjectWrapper;
use DataMap\Input\RecursiveInput;
use PhpSpec\ObjectBehavior;
use function array_merge;

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

    function it_can_be_extended_with_wrappers()
    {
        $inner1 = new ObjectWrapper();
        $this->beConstructedWith($inner1);
        $object = new \stdClass();
        $object->one = new \stdClass();
        $object->one->two = 'three';
        $array = ['one' => ['two' => 'three']];

        $this->supportedTypes()->shouldBe($inner1->supportedTypes());
        $this->wrap($object)->get('one.two')->shouldBe('three');
        $this->shouldThrow(FailedToWrapInput::class)->during('wrap', [$array]);

        $inner2 = new ArrayWrapper();
        $extended = $this->withWrappers($inner2);
        $extended->supportedTypes()->shouldBe(array_merge($inner1->supportedTypes(), $inner2->supportedTypes()));
        $extended->wrap($array)->get('one.two')->shouldBe('three');
    }
}
