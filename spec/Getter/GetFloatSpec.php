<?php

namespace spec\DataMap\Getter;

use DataMap\Input\ArrayInput;
use PhpSpec\ObjectBehavior;

final class GetFloatSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('key');
    }

    function it_casts_numeric_value_to_float()
    {
        $this(new ArrayInput(['key' => 12]))->shouldBe((float)12);
        $this(new ArrayInput(['key' => '12.34']))->shouldBe((float)'12.34');
        $this(new ArrayInput(['key' => 12.34]))->shouldBe(12.34);
    }

    function it_returns_null_by_default_when_value_is_undefined_or_not_numeric()
    {
        $this(new ArrayInput(['key' => null]))->shouldBe(null);
        $this(new ArrayInput([]))->shouldBe(null);
        $this(new ArrayInput(['key' => 'x123']))->shouldBe(null);
    }

    function it_can_return_default_value_when_input_value_is_undefined_or_not_numeric()
    {
        $this->beConstructedWith('key', 0.123);

        $this(new ArrayInput(['key' => null]))->shouldBe(0.123);
        $this(new ArrayInput([]))->shouldBe(0.123);
        $this(new ArrayInput(['key' => 'x123']))->shouldBe(0.123);
    }
}
