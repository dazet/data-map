<?php

namespace spec\DataMap\Getter;

use DataMap\Input\ArrayInput;
use PhpSpec\ObjectBehavior;

final class GetTranslatedSpec extends ObjectBehavior
{
    function it_translates_value()
    {
        $this->beConstructedWith('key', ['a' => 'trans(a)', 'b' => 'trans(b)']);

        $this(new ArrayInput(['key' => 'a']))->shouldBe('trans(a)');
        $this(new ArrayInput(['key' => 'b']))->shouldBe('trans(b)');
    }

    function it_returns_null_when_translation_not_defined()
    {
        $this->beConstructedWith('key', ['a' => 'trans(a)']);

        $this(new ArrayInput(['key' => 'a']))->shouldBe('trans(a)');
        $this(new ArrayInput(['key' => 'b']))->shouldBe(null);
        $this(new ArrayInput(['key' => ['a']]))->shouldBe(null);
    }

    function it_can_return_default_value_when_translation_not_defined()
    {
        $this->beConstructedWith('key', ['a' => 'trans(a)'], 'default');

        $this(new ArrayInput(['key' => 'a']))->shouldBe('trans(a)');
        $this(new ArrayInput(['key' => 'b']))->shouldBe('default');
        $this(new ArrayInput(['key' => ['a']]))->shouldBe('default');
    }
}
