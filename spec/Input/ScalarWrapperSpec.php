<?php

namespace spec\DataMap\Input;

use DataMap\Input\NullInput;
use PhpSpec\ObjectBehavior;

final class ScalarWrapperSpec extends ObjectBehavior
{
    function it_supports_scalar_types()
    {
        $this->supportedTypes()->shouldContain('null');
        $this->supportedTypes()->shouldContain('string');
        $this->supportedTypes()->shouldContain('integer');
        $this->supportedTypes()->shouldContain('double');
        $this->supportedTypes()->shouldContain('boolean');
    }

    function it_wraps_all_with_null_input()
    {
        $this->wrap('string')->shouldBeLike(new NullInput());
        $this->wrap(123)->shouldBeLike(new NullInput());
        $this->wrap(1.23)->shouldBeLike(new NullInput());
        $this->wrap(null)->shouldBeLike(new NullInput());
        $this->wrap(false)->shouldBeLike(new NullInput());
    }
}
