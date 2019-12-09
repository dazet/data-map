<?php

namespace spec\DataMap\Input;

use DataMap\Exception\FailedToWrapInput;
use DataMap\Input\ArrayInput;
use DataMap\Input\ArrayWrapper;
use DataMap\Input\ExtensibleWrapper;
use DataMap\Input\Input;
use DataMap\Input\NullInput;
use DataMap\Input\ObjectInput;
use DataMap\Input\ObjectWrapper;
use DataMap\Input\Wrapper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\DataMap\Stub\UserDto;
use spec\DataMap\Stub\UserInterface;
use spec\DataMap\Stub\UserValue;

final class MixedWrapperSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('default');
    }

    function it_can_wraps_array_with_ArrayInput_by_default()
    {
        $this->wrap(['key' => 'value'])->shouldHaveType(ArrayInput::class);
    }

    function it_wraps_object_with_ObjectInput_by_default()
    {
        $this->wrap(new \stdClass())->shouldHaveType(ObjectInput::class);
        $this->wrap(new UserDto())->shouldHaveType(ObjectInput::class);
    }

    function it_wraps_scalar_values_with_NullInput_by_default()
    {
        $this->wrap(null)->shouldHaveType(NullInput::class);
        $this->wrap('string')->shouldHaveType(NullInput::class);
        $this->wrap(1)->shouldHaveType(NullInput::class);
        $this->wrap(1.0)->shouldHaveType(NullInput::class);
        $this->wrap(true)->shouldHaveType(NullInput::class);
    }

    function it_can_be_created_with_narrowed_set_of_wrappers()
    {
        $this->beConstructedWith(new ArrayWrapper());

        $this->wrap(['key' => 'value'])->shouldHaveType(ArrayInput::class);

        $this->shouldThrow(FailedToWrapInput::class)->during('wrap', [new UserDto()]);
    }

    function it_favours_specific_class_wrapper_over_generic_object_wrapper(Wrapper $userWrapper)
    {
        $userDto = new UserDto();
        $userDtoInput = new ObjectInput($userDto);

        $userWrapper->supportedTypes()->willReturn([UserDto::class]);
        $userWrapper->wrap($userDto)->shouldBeCalled()->willReturn($userDtoInput);

        $this->beConstructedWith(new ObjectWrapper(), $userWrapper);

        $this->wrap($userDto)->shouldReturn($userDtoInput);
    }

    function it_favours_interface_wrapper_over_generic_object_wrapper(Wrapper $userWrapper)
    {
        $user = new UserValue('id', 'name', 33);
        $userInput = new ObjectInput($user);

        $userWrapper->supportedTypes()->willReturn([UserInterface::class]);
        $userWrapper->wrap($user)->shouldBeCalled()->willReturn($userInput);

        $this->beConstructedWith(new ObjectWrapper(), $userWrapper);

        $this->wrap($user)->shouldReturn($userInput);
    }

    function it_favours_class_wrapper_over_interface_wrapper(Wrapper $classWrapper, Wrapper $interfaceWrapper)
    {
        $user = new UserValue('id', 'name', 33);
        $userInput = new ObjectInput($user);

        $classWrapper->supportedTypes()->willReturn([UserValue::class]);
        $classWrapper->wrap($user)->shouldBeCalled()->willReturn($userInput);

        $interfaceWrapper->supportedTypes()->willReturn([UserInterface::class]);
        $interfaceWrapper->wrap($user)->shouldNotBeCalled();

        $this->beConstructedWith(new ObjectWrapper(), $classWrapper, $interfaceWrapper);

        $this->wrap($user)->shouldReturn($userInput);
    }

    function it_can_be_fluently_copied_with_appended_wrappers(Wrapper $userWrapper, Input $userInput)
    {
        $objectWrapper = new ObjectWrapper();
        $this->beConstructedWith($objectWrapper);

        $user = new UserDto();
        $userWrapper->supportedTypes()->willReturn([UserDto::class]);
        $userWrapper->wrap($user)->shouldBeCalled()->willReturn($userInput);

        $extended = $this->withWrappers($userWrapper);

        $this->wrap($user)->shouldBeLike(new ObjectInput($user));
        $extended->wrap($user)->shouldReturn($userInput);
    }

    function it_uses_last_appended_wrapper_when_there_are_multiple_wrappers_for_the_same_type(
        Wrapper $wrapper1,
        Input $input1,
        Wrapper $wrapper2,
        Input $input2
    ) {
        $wrapper1->supportedTypes()->willReturn([UserDto::class, 'array']);
        $wrapper1->wrap(Argument::any())->willReturn($input1);
        $wrapper2->supportedTypes()->willReturn([UserDto::class]);
        $wrapper2->wrap(Argument::any())->willReturn($input2);

        $this->beConstructedWith($wrapper1, $wrapper2);

        $this->wrap(new UserDto())->shouldReturn($input2);
        $this->wrap([])->shouldReturn($input1);
    }

    function it_has_factory_method_for_creating_extended_wrapper_from_regular_wrapper(
        Wrapper $wrapper,
        Wrapper $extension1,
        Wrapper $extension2
    ) {
        $wrapper->supportedTypes()->willReturn(['array']);
        $extension1->supportedTypes()->willReturn(['string']);
        $extension2->supportedTypes()->willReturn(['int']);

        $this->beConstructedThrough('extend', [$wrapper, $extension1, $extension2]);

        $this->supportedTypes()->shouldBe(['array', 'string', 'int']);
    }

    function it_reuses_extensible_wrapper_when(
        ExtensibleWrapper $wrapper,
        Wrapper $extension1,
        Wrapper $extension2,
        ExtensibleWrapper $extended
    ) {
        $wrapper->withWrappers($extension1, $extension2)->shouldBeCalled()->willReturn($extended);
        $extended->supportedTypes()->willReturn(['array', 'int']);

        $this->beConstructedThrough('extend', [$wrapper, $extension1, $extension2]);
        $this->supportedTypes()->shouldBe(['array', 'int']);
    }
}
