<?php

namespace spec\DataMap\Getter;

use DataMap\Input\ArrayInput;
use PhpSpec\ObjectBehavior;

final class GetBooleanSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('key');
    }

    function it_maps_popular_boolean_forms_to_strict_boolean_true()
    {
        $this(new ArrayInput(['key' => true]))->shouldBe(true);
        $this(new ArrayInput(['key' => 1]))->shouldBe(true);
        $this(new ArrayInput(['key' => '1']))->shouldBe(true);
    }

    function it_maps_popular_boolean_forms_to_strict_boolean_false()
    {
        $this(new ArrayInput(['key' => false]))->shouldBe(false);
        $this(new ArrayInput(['key' => 0]))->shouldBe(false);
        $this(new ArrayInput(['key' => '0']))->shouldBe(false);
    }

    function it_returns_null_when_key_not_available_or_null()
    {
        $this(new ArrayInput([]))->shouldBe(null);
        $this(new ArrayInput(['key' => null]))->shouldBe(null);
    }

    function it_returns_default_value_when_key_not_available_or_null()
    {
        $this->beConstructedWith('key', true);

        $this(new ArrayInput([]))->shouldBe(true);
        $this(new ArrayInput(['key' => null]))->shouldBe(true);
    }
}
