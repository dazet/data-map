<?php

namespace spec\DataMap\Common;

use ArrayIterator;
use Generator;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use spec\DataMap\Stub\DummyCountable;

final class ArrayUtilSpec extends ObjectBehavior
{
    function it_tells_that_array_can_be_array()
    {
        $this::canBeArray(['one'])->shouldBe(true);
    }

    function it_tells_that_iterable_can_be_array()
    {
        $this::canBeArray(new ArrayIterator(['one']))->shouldBe(true);
    }

    function it_says_that_Generator_can_be_array()
    {
        $traverse = function (): Generator {
            yield 'one';
            yield 'two';
        };

        $this::canBeArray($traverse())->shouldBe(true);
    }

    function it_returns_array()
    {
        $this::toArray(['one'])->shouldReturn(['one']);
        $this::toArrayOrNull(['one'])->shouldReturn(['one']);
    }

    function it_casts_iterable_to_array()
    {
        $iterable = new ArrayIterator(['one', 'two', 'three']);
        $this::toArray($iterable)->shouldReturn(['one', 'two', 'three']);
        $this::toArrayOrNull($iterable)->shouldReturn(['one', 'two', 'three']);
    }

    function it_does_not_cast_scalar_values_to_array()
    {
        foreach (['string', 120, 120.0, false, true, null] as $value) {
            $this::toArrayOrNull($value)->shouldReturn(null);
            $this->shouldThrow(InvalidArgumentException::class)->during('toArray', [$value]);
        }
    }

    function it_tells_that_array_is_countable()
    {
        $this::isCountable([1, 2])->shouldBe(true);
    }

    function it_returns_array_count()
    {
        $this::countOrNull([1, 2, 3, 4])->shouldBe(4);
    }

    function it_tells_that_Countable_is_countable()
    {
        $this::isCountable(new DummyCountable())->shouldBe(true);
    }

    function it_returns_Countable_count()
    {
        $this::countOrNull(new DummyCountable(123))->shouldBe(123);
    }
}
