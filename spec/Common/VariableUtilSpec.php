<?php

namespace spec\DataMap\Common;

use PhpSpec\ObjectBehavior;

final class VariableUtilSpec extends ObjectBehavior
{
    function it_returns_default_when_value_is_empty()
    {
        $emptyValues = [
            0,
            '',
            null,
            false,
            [],
            '0',
        ];

        foreach ($emptyValues as $emptyValue) {
            $this::ifEmpty($emptyValue, 'default')->shouldReturn('default');
        }
    }

    function it_returns_value_when_value_is_not_empty()
    {
        $notEmpties = [
            1,
            'a',
            true,
            ['a'],
            '1',
            new \stdClass(),
        ];

        foreach ($notEmpties as $notEmpty) {
            $this::ifEmpty($notEmpty, 'default')->shouldReturn($notEmpty);
        }
    }

    function it_returns_default_when_value_is_null()
    {
        $this::ifNull(null, 'default')->shouldReturn('default');
    }

    function it_returns_value_when_value_is_not_null()
    {
        $notNulls = [
            0,
            1,
            'a',
            '',
            true,
            false,
            [],
            ['a'],
            '0',
            '1',
            new \stdClass(),
        ];

        foreach ($notNulls as $notNull) {
            $this::ifNull($notNull, 'default')->shouldReturn($notNull);
        }
    }
}
