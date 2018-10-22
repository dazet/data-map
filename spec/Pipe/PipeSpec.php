<?php

namespace spec\DataMap\Pipe;

use PhpSpec\ObjectBehavior;
use spec\DataMap\Stub\DummyCallable;

final class PipeSpec extends ObjectBehavior
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

    function it_can_have_predefined_arguments_and_pipe_argument_is_first_by_default()
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

    function it_can_have_predefined_arguments_and_custom_pipe_argument_position()
    {
        $this->beConstructedWith('str_replace', ['X', 'Y', '$$']);

        $this('this is letter X')->shouldReturn('this is letter Y');
    }

    function it_can_be_copied_with_modified_default_argument(DummyCallable $callable)
    {
        $this->beConstructedWith($callable, ['X', '$$', 'Y']);

        $copiedPipe = $this->withArgs(['_', '$$', 'Z']);
        $copiedPipe->shouldNotBeLike($this);

        $callable->__invoke('X', 'value', 'Z')->shouldBeCalled()->willReturn('result');
        $copiedPipe('value')->shouldReturn('result');
    }
}
