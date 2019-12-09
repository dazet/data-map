<?php

namespace spec\DataMap\Common;

use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use spec\DataMap\Stub\DummyCountable;
use spec\DataMap\Stub\StringObject;

final class StringUtilSpec extends ObjectBehavior
{
    function it_can_be_string_when_is_scalar()
    {
        $scalars = [
            'string',
            true,
            123,
            123.45,
        ];

        foreach ($scalars as $scalar) {
            $this::canBeString($scalar)->shouldReturn(true);
        }
    }

    function it_can_be_string_when_is_stringable_object()
    {
        $this::canBeString(new StringObject('string'))->shouldReturn(true);
    }

    function it_cannot_be_string_when_null_or_array_or_plain_object()
    {
        $notStrings = [
            null,
            [],
            new DummyCountable(),
            new class {
            },
            function () {
            },
            (function () {
                yield 1;
            })(),
        ];

        foreach ($notStrings as $notString) {
            $this::canBeString($notString)->shouldReturn(false);
        }
    }

    function it_transforms_scalars_to_string()
    {
        $scalars = [
            ['string', 'string'],
            [true, '1'],
            [false, '0'],
            [123, '123'],
            [123.45, '123.45'],
        ];

        foreach ($scalars as [$scalar, $string]) {
            $this::toStringOrNull($scalar)->shouldReturn($string);
            $this::toString($scalar)->shouldReturn($string);
        }
    }

    function it_transforms_stringable_object()
    {
        $string = new StringObject('string');
        $this::toStringOrNull($string)->shouldReturn('string');
        $this::toString($string)->shouldReturn('string');
    }

    function it_does_not_transform_null_or_array_or_plain_object()
    {
        $notStrings = [
            null,
            [],
            new DummyCountable(),
            new class {
            },
            function () {
            },
            (function () {
                yield 1;
            })(),
        ];

        foreach ($notStrings as $notString) {
            $this::toStringOrNull($notString)->shouldReturn(null);
            $this->shouldThrow(InvalidArgumentException::class)->during('toString', [$notString]);
        }
    }
}
