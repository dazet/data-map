<?php

namespace DataMap\Common;

final class DateUtil
{
    private function __construct()
    {
    }

    public static function canBeDate($value): bool
    {
        return $value instanceof \DateTimeInterface
            || (StringUtil::canBeString($value) && \strtotime(StringUtil::toString($value)) !== false);
    }

    public static function toDatetimeOrNull($value): ?\DateTimeImmutable
    {
        if ($value === null || !self::canBeDate($value)) {
            return null;
        }

        if ($value instanceof \DateTime) {
            return \DateTimeImmutable::createFromMutable($value);
        }

        if ($value instanceof \DateTimeImmutable) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            $format = 'Y-m-d H:i:s.u';

            return \DateTimeImmutable::createFromFormat(
                $format,
                $value->format($format),
                $value->getTimezone()
            ) ?: null;
        }

        if (\strtotime((string)$value) !== false) {
            return new \DateTimeImmutable((string)$value);
        }

        return null;
    }

    public static function toDatetime($value): \DateTimeImmutable
    {
        $value = self::toDatetimeOrNull($value);

        if ($value === null) {
            throw new \InvalidArgumentException('Given value cannot be casted to datetime');
        }

        return $value;
    }

    public static function toDateFormatOrNull($value, string $format): ?string
    {
        $datetime = self::toDatetimeOrNull($value);

        if ($datetime === null) {
            return null;
        }

        return $datetime->format($format);
    }

    public static function toDateFormat($value, string $format): string
    {
        return self::toDatetime($value)->format($format);
    }

    public static function toTimestampOrNull($value): ?int
    {
        if (\is_int($value)) {
            return $value;
        }

        if (\is_numeric($value)) {
            return NumberUtil::toInt($value);
        }

        if (self::canBeDate($value)) {
            return self::toDatetime($value)->getTimestamp();
        }

        return null;
    }

    public static function dateModifyOrNull($value, string $modifier): ?\DateTimeImmutable
    {
        $datetime = self::toDatetimeOrNull($value);

        if ($datetime === null) {
            return null;
        }

        return $datetime->modify($modifier);
    }
}
