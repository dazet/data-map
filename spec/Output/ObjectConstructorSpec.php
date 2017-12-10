<?php

namespace spec\DataMap\Output;

use PhpSpec\ObjectBehavior;
use spec\DataMap\Stub\ImmutableClass;
use spec\DataMap\Stub\ImmutableFactoryMethodClass;

final class ObjectConstructorSpec extends ObjectBehavior
{
    function it_creates_object_from_array_through_constructor()
    {
        $this->beConstructedWith(ImmutableClass::class);

        $this
            ->format(['one' => 'x1', 'two' => 'x2', 'three' => 'x3'])
            ->shouldBeLike(new ImmutableClass('x1', 'x2', 'x3'));
    }

    function it_creates_object_from_array_through_static_factory_method()
    {
        $this->beConstructedWith(ImmutableFactoryMethodClass::class, 'create');

        $this
            ->format(['one' => 'x1', 'two' => 'x2', 'three' => 'x3'])
            ->shouldBeLike(ImmutableFactoryMethodClass::create('x1', 'x2', 'x3'));
    }

    function it_keeps_valid_parameters_order()
    {
        $this->beConstructedWith(ImmutableClass::class);

        $this
            ->format(['three' => 'x3', 'one' => 'x1', 'two' => 'x2'])
            ->shouldBeLike(new ImmutableClass('x1', 'x2', 'x3'));
    }

    function it_passes_null_when_value_not_available()
    {
        $this->beConstructedWith(ImmutableClass::class);

        $this
            ->format(['three' => 'x3', 'two' => 'x2'])
            ->shouldBeLike(new ImmutableClass(null, 'x2', 'x3'));
    }

    function it_passes_null_when_value_not_available_when_creating_through_factory_method()
    {
        $this->beConstructedWith(ImmutableFactoryMethodClass::class, 'create');

        $this
            ->format(['one' => 'x1', 'three' => 'x3'])
            ->shouldBeLike(ImmutableFactoryMethodClass::create('x1', null, 'x3'));
    }

    function it_throws_InvalidArgumentException_when_class_does_not_exist()
    {
        $this->beConstructedWith('...');
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_throws_InvalidArgumentException_when_class_constructor_does_not_exist()
    {
        $this->beConstructedWith(ImmutableFactoryMethodClass::class, 'zonk');
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_throws_InvalidArgumentException_when_class_constructor_is_not_public()
    {
        $this->beConstructedWith(ImmutableFactoryMethodClass::class);
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_throws_InvalidArgumentException_when_requested_method_is_not_constructor_or_static_method()
    {
        $this->beConstructedWith(ImmutableFactoryMethodClass::class, 'getOne');
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }
}
