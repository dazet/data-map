<?php

namespace spec\DataMap\Common;

use DataMap\Common\BooleanUtil;
use PhpSpec\ObjectBehavior;
use function array_merge;

final class BooleanUtilSpec extends ObjectBehavior
{
    function it_tells_that_value_can_be_bool()
    {
        foreach (array_merge(BooleanUtil::TRUTHS, BooleanUtil::FALLACY) as $truth) {
            $this::canBeBool($truth)->shouldBe(true);
        }
    }

    function it_tells_that_value_cannot_be_bool()
    {
        foreach (self::nonBooleans() as $truth) {
            $this::canBeBool($truth)->shouldBe(false);
        }
    }

    function it_casts_true_values_to_boolean_true()
    {
        foreach (BooleanUtil::TRUTHS as $truth) {
            $this::toBoolOrNull($truth)->shouldBe(true);
        }
    }

    function it_casts_false_values_to_boolean_false()
    {
        foreach (BooleanUtil::FALLACY as $fallacy) {
            $this::toBoolOrNull($fallacy)->shouldBe(false);
        }
    }

    function it_does_not_cast_ambiguous_values_to_boolean()
    {
        foreach (self::nonBooleans() as $notBool) {
            $this::toBoolOrNull($notBool)->shouldBe(null);
        }
    }

    private static function nonBooleans(): array
    {
        return [-1, 2, '', null, '01', '-', ' ', 0.0, 1.0, [], new \stdClass()];
    }
}
