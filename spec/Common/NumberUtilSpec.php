<?php

namespace spec\DataMap\Common;

use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use spec\DataMap\Stub\StringObject;

final class NumberUtilSpec extends ObjectBehavior
{
    function it_can_be_number_when_is_numeric()
    {
        $this::canBeNumber(12)->shouldReturn(true);
        $this::canBeNumber(-12)->shouldReturn(true);
        $this::canBeNumber(12.34)->shouldReturn(true);
        $this::canBeNumber(-12.34)->shouldReturn(true);
    }

    function it_can_be_number_when_is_boolean()
    {
        $this::canBeNumber(true)->shouldReturn(true);
        $this::canBeNumber(false)->shouldReturn(true);
    }

    function it_can_be_number_when_is_numeric_string()
    {
        $this::canBeNumber('12')->shouldReturn(true);
        $this::canBeNumber('-12')->shouldReturn(true);
        $this::canBeNumber('12.34')->shouldReturn(true);
        $this::canBeNumber('-12.34')->shouldReturn(true);
    }

    function it_can_be_number_when_is_numeric_stringable_object()
    {
        $this::canBeNumber(new StringObject('12.34'))->shouldReturn(true);
    }

    function it_can_transfer_numeric_to_int()
    {
        $tests = [
            [123, 123],
            [123.1, 123],
            [123.9, 123],
            ['123', 123],
            ['-123', -123],
            ['123.0', 123],
            [new StringObject('123'), 123],
        ];

        foreach ($tests as [$numeric, $integer]) {
            $this::toInt($numeric)->shouldReturn($integer);
            $this::toIntOrNull($numeric)->shouldReturn($integer);
        }
    }

    function it_will_not_transform_non_numeric_values_to_int()
    {
        $notNumbers = [
            null,
            '',
            'abc',
            new StringObject(''),
            [],
        ];

        foreach ($notNumbers as $notNumber) {
            $this::toIntOrNull($notNumber)->shouldReturn(null);
            $this->shouldThrow(InvalidArgumentException::class)->during('toInt', [$notNumber]);
        }
    }

    function it_can_transfer_numeric_to_float()
    {
        $tests = [
            [123, 123.0],
            [123.1, 123.1],
            [123.9, 123.9],
            ['123', 123.0],
            ['-123', -123.0],
            ['123.45', 123.45],
            [new StringObject('123.4'), 123.4],
        ];

        foreach ($tests as [$numeric, $integer]) {
            $this::toFloat($numeric)->shouldReturn($integer);
            $this::toFloatOrNull($numeric)->shouldReturn($integer);
        }
    }

    function it_will_not_transform_non_numeric_values_to_float()
    {
        $notNumbers = [
            null,
            '',
            'abc',
            new StringObject(''),
            [],
        ];

        foreach ($notNumbers as $notNumber) {
            $this::toFloatOrNull($notNumber)->shouldReturn(null);
            $this->shouldThrow(InvalidArgumentException::class)->during('toFloat', [$notNumber]);
        }
    }
}
