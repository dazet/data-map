<?php

namespace spec\DataMap\Getter;

use DataMap\Input\ArrayInput;
use PhpSpec\ObjectBehavior;

final class GetStringSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('key');
    }

    function it_casts_values_to_string()
    {
        $this(new ArrayInput(['key' => 12]))->shouldBe('12');
        $this(new ArrayInput(['key' => '12.34']))->shouldBe('12.34');
        $this(new ArrayInput(['key' => 12.34]))->shouldBe('12.34');
        $this(new ArrayInput(['key' => false]))->shouldBe('');
        $this(new ArrayInput(['key' => true]))->shouldBe('1');
    }

    function it_casts_stringable_object()
    {
        $object = new class
        {
            public function __toString(): string
            {
                return 'string';
            }
        };

        $this(new ArrayInput(['key' => $object]))->shouldBe('string');
    }

    function it_returns_null_by_default_when_value_is_undefined_or_cannot_be_casted()
    {
        $this(new ArrayInput(['key' => null]))->shouldBe(null);
        $this(new ArrayInput([]))->shouldBe(null);
        $this(new ArrayInput(['key' => ['array']]))->shouldBe(null);
    }

    function it_can_return_default_value_when_input_value_is_undefined_or_not_numeric()
    {
        $this->beConstructedWith('key', 'default');

        $this(new ArrayInput(['key' => null]))->shouldBe('default');
        $this(new ArrayInput([]))->shouldBe('default');
        $this(new ArrayInput(['key' => ['array']]))->shouldBe('default');
    }
}
