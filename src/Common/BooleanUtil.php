<?php declare(strict_types=1);

namespace DataMap\Common;

use function array_merge;
use function in_array;

final class BooleanUtil
{
    public const TRUTHS = [true, 1, '1'];
    public const FALLACY = [false, 0, '0'];
    /** @var callable */
    public const toBoolOrNull = [self::class, 'toBoolOrNull'];

    /**
     * @param mixed $value
     */
    public static function canBeBool($value): bool
    {
        return in_array($value, array_merge(self::TRUTHS, self::FALLACY), true);
    }

    /**
     * @param mixed $value
     */
    public static function toBoolOrNull($value): ?bool
    {
        if (in_array($value, self::TRUTHS, true)) {
            return true;
        }

        if (in_array($value, self::FALLACY, true)) {
            return false;
        }

        return null;
    }
}
