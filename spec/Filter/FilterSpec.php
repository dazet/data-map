<?php

namespace spec\DataMap\Filter;

use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use spec\DataMap\Stub\DummyCallable;
use function trim;

final class FilterSpec extends ObjectBehavior
{
    function it_can_be_a_function_that_takes_1_argument()
    {
        $this->beConstructedWith('trim');

        $string = '   space   ';
        $this($string)->shouldReturn(trim($string));
    }

    function it_can_be_a_callable_that_takes_1_argument(DummyCallable $callable)
    {
        $this->beConstructedWith($callable);

        $callable->__invoke('value')->shouldBeCalled()->willReturn('result');

        $this('value')->shouldReturn('result');
    }

    function it_can_have_predefined_arguments_and_filter_argument_is_first_by_default()
    {
        $this->beConstructedWith('trim', ['.']);

        $string = '... dots ...';
        $this($string)->shouldReturn(trim($string, '.'));
    }

    function it_can_be_a_callable_with_predefined_arguments(DummyCallable $callable)
    {
        $this->beConstructedWith($callable, ['arg1', 'arg2']);

        $callable->__invoke('value', 'arg1', 'arg2')->shouldBeCalled()->willReturn('result');

        $this('value')->shouldReturn('result');
    }

    function it_can_have_predefined_arguments_and_custom_filter_argument_position()
    {
        $this->beConstructedWith('str_replace', ['X', 'Y', '$$']);

        $this('this is letter X')->shouldReturn('this is letter Y');
    }

    function it_can_be_copied_with_modified_default_argument(DummyCallable $callable)
    {
        $this->beConstructedWith($callable, ['X', '$$', 'Y']);

        $copiedFilter = $this->withArgs(['_', '$$', 'Z']);
        $copiedFilter->shouldNotBeLike($this);

        $callable->__invoke('X', 'value', 'Z')->shouldBeCalled()->willReturn('result');
        $copiedFilter('value')->shouldReturn('result');
    }

    function it_remembers_variable_argument_position_when_copied(DummyCallable $callable)
    {
        $this->beConstructedWith($callable, ['X', '$$']);

        $copiedFilter = $this->withArgs(['Y']);
        $copiedFilter->shouldNotBeLike($this);

        $callable->__invoke('Y', 'value')->shouldBeCalled()->willReturn('result');
        $copiedFilter('value')->shouldReturn('result');
    }

    function it_throws_InvalidArgumentException_when_trying_to_copy_with_value_in_place_of_variable(
        DummyCallable $callable
    ) {
        $this->beConstructedWith($callable, ['X', '$$']);
        $this->shouldThrow(InvalidArgumentException::class)->during('withArgs', [['Y', 'Z']]);
    }

    function it_returns_same_instance_when_wrapping_self()
    {
        $this->beConstructedThrough('wrap', ['trim']);
        $this::wrap($this)->shouldReturn($this);
    }

    function it_returns_same_instance_when_wrapping_nullable_self()
    {
        $this->beConstructedThrough('nullable', [new DummyCallable()]);
        $this::nullable($this)->shouldReturn($this);
    }
}
