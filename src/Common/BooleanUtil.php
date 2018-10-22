<?php

namespace DataMap\Common;

final class BooleanUtil
{
    public const TRUTHS = [true, 1, '1', 'true', 'TRUE', 'True'];
    public const FALLACY = [false, 0, '0', 'false', 'FALSE', 'False'];

    private function __construct()
    {
    }

    public static function canBeBool($value): bool
    {
        return \in_array($value, \array_merge(self::TRUTHS, self::FALLACY), true);
    }

    public static function toBoolOrNull($value): ?bool
    {
        if (\in_array($value, self::TRUTHS, true)) {
            return true;
        }

        if (\in_array($value, self::FALLACY, true)) {
            return false;
        }

        return null;
    }
}
