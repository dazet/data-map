<?php

namespace spec\DataMap\Input;

use PhpSpec\ObjectBehavior;

final class ArrayInputSpec extends ObjectBehavior
{
    function it_wraps_array_input()
    {
        $this->beConstructedWith(['x' => 'a', 'y' => 'b']);

        $this->get('x')->shouldBe('a');
        $this->get('y')->shouldBe('b');
        $this->get('z')->shouldBe(null);

        $this->has('x')->shouldBe(true);
        $this->has('y')->shouldBe(true);
        $this->has('z')->shouldBe(false);
    }

    function it_wraps_ArrayAccess_input()
    {
        $this->beConstructedWith(new \ArrayObject(['x' => 'a', 'y' => 'b']));

        $this->get('x')->shouldBe('a');
        $this->get('y')->shouldBe('b');
        $this->get('z')->shouldBe(null);

        $this->has('x')->shouldBe(true);
        $this->has('y')->shouldBe(true);
        $this->has('z')->shouldBe(false);
    }

    function it_can_return_default_value()
    {
        $this->beConstructedWith(['x' => 'a']);

        $this->get('x', 'default')->shouldBe('a');
        $this->get('y', 'default')->shouldBe('default');
    }
}
