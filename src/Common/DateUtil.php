<?php declare(strict_types=1);

namespace DataMap\Common;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;
use function ctype_digit;
use function is_int;
use function is_string;
use function strtotime;

final class DateUtil
{
    /** @var callable */
    public const canBeDate = [self::class, 'canBeDate'];
    /** @var callable */
    public const toDatetimeOrNull = [self::class, 'toDatetimeOrNull'];
    /** @var callable */
    public const toDatetime = [self::class, 'toDatetime'];
    /** @var callable */
    public const toDateFormatOrNull = [self::class, 'toDateFormatOrNull'];
    /** @var callable */
    public const toDateFormat = [self::class, 'toDateFormat'];
    /** @var callable */
    public const toTimestampOrNull = [self::class, 'toTimestampOrNull'];
    /** @var callable */
    public const dateModifyOrNull = [self::class, 'dateModifyOrNull'];

    /**
     * @param mixed $value
     */
    public static function canBeDate($value): bool
    {
        return $value instanceof DateTimeInterface
            || (StringUtil::canBeString($value) && strtotime(StringUtil::toString($value)) !== false);
    }

    /**
     * @param mixed $value
     */
    public static function toDatetimeOrNull($value, ?DateTimeZone $timeZone = null): ?DateTimeImmutable
    {
        if ($value === null || !self::canBeDate($value)) {
            return null;
        }

        if ($value instanceof DateTime) {
            return DateTimeImmutable::createFromMutable($value);
        }

        if ($value instanceof DateTimeImmutable) {
            return $value;
        }

        return new DateTimeImmutable((string)$value, $timeZone);
    }

    /**
     * @param mixed $value
     */
    public static function toDatetime($value, ?DateTimeZone $timeZone = null): DateTimeImmutable
    {
        $value = self::toDatetimeOrNull($value, $timeZone);

        if ($value === null) {
            throw new InvalidArgumentException('Given value cannot be casted to datetime');
        }

        return $value;
    }

    /**
     * @param mixed $value
     */
    public static function toDateFormatOrNull($value, string $format): ?string
    {
        $datetime = self::toDatetimeOrNull($value);

        if ($datetime === null) {
            return null;
        }

        return $datetime->format($format);
    }

    /**
     * @param mixed $value
     */
    public static function toDateFormat($value, string $format): string
    {
        return self::toDatetime($value)->format($format);
    }

    /**
     * @param mixed $value
     */
    public static function toTimestampOrNull($value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && ctype_digit($value)) {
            return NumberUtil::toInt($value);
        }

        if (self::canBeDate($value)) {
            return self::toDatetime($value)->getTimestamp();
        }

        return null;
    }

    /**
     * @param mixed $value
     */
    public static function dateModifyOrNull($value, string $modifier): ?DateTimeImmutable
    {
        $datetime = self::toDatetimeOrNull($value);

        if ($datetime === null) {
            return null;
        }

        return $datetime->modify($modifier);
    }
}
