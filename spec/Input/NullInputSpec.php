<?php

namespace spec\DataMap\Input;

use PhpSpec\ObjectBehavior;

final class NullInputSpec extends ObjectBehavior
{
    function it_returns_null_for_every_key()
    {
        $this->get('')->shouldBe(null);
        $this->get('0')->shouldBe(null);
        $this->get('key')->shouldBe(null);
    }

    function it_can_return_default_value_for_every_key()
    {
        $this->get('', 'default')->shouldBe('default');
        $this->get('0', 'default')->shouldBe('default');
        $this->get('key', 'default')->shouldBe('default');
    }

    function it_does_not_have_any_key()
    {
        $this->has('')->shouldBe(false);
        $this->has('0')->shouldBe(false);
        $this->has('key')->shouldBe(false);
    }
}
