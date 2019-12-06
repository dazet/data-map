<?php

namespace spec\DataMap\Common;

use DataMap\Common\DateUtil;
use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;

final class DateUtilSpec extends ObjectBehavior
{
    function it_tells_that_DateTime_can_be_date()
    {
        $this::canBeDate(new DateTime())->shouldBe(true);
    }

    function it_tells_that_DateTimeImmutable_can_be_date()
    {
        $this::canBeDate(new DateTimeImmutable())->shouldBe(true);
    }

    function it_tells_that_any_string_supported_by_strtotime_can_be_date()
    {
        $dateStrings = [
            'today',
            '2000-01-01',
            '13:00',
            'next Monday',
            '+1 day',
        ];

        foreach ($dateStrings as $dateString) {
            $this::canBeDate($dateString)->shouldBe(true);
        }

    }

    function it_tells_that_unsupported_values_cannot_be_date()
    {
        $notDates = ['someday', '', '1', null, 10];

        foreach ($notDates as $notDate) {
            $this::canBeDate($notDate)->shouldBe(false);
        }
    }

    function it_transforms_DateTime_to_DateTimeImmutable()
    {
        $mutable = new DateTime();
        $immutable = DateTimeImmutable::createFromMutable($mutable);
        $this::toDatetime($mutable)->shouldBeLike($immutable);
        $this::toDatetimeOrNull($mutable)->shouldBeLike($immutable);
    }

    function it_makes_no_transformation_on_DateTimeImmutable()
    {
        $immutable = new DateTimeImmutable();
        $this::toDatetime($immutable)->shouldReturn($immutable);
        $this::toDatetimeOrNull($immutable)->shouldReturn($immutable);
    }

    function it_transforms_time_string_to_DateTimeImmutable()
    {
        $dates = [
            '2000-01-01',
            'yesterday',
            '+1 month 12:00',
            'next Monday',
        ];

        foreach ($dates as $date) {
            $this::toDatetime($date)->shouldBeLike(new DateTimeImmutable($date));
            $this::toDatetimeOrNull($date)->shouldBeLike(new DateTimeImmutable($date));
        }
    }

    function it_returns_null_when_value_cannot_be_transformed_to_date()
    {
        $nonDates = [
            'hello',
            123,
            'yes',
            null,
            '',
        ];

        foreach ($nonDates as $nonDate) {
            $this::toDatetimeOrNull($nonDate)->shouldBeNull();
            $this::shouldThrow(InvalidArgumentException::class)->during('toDatetime', [$nonDate]);
        }
    }

    function it_can_throw_InvalidArgumentException_when_value_cannot_be_transformed_to_date()
    {
        $nonDates = [
            'hello',
            123,
            'yes',
            null,
            '',
        ];

        foreach ($nonDates as $nonDate) {
            $this::shouldThrow(InvalidArgumentException::class)->during('toDatetime', [$nonDate]);
        }
    }

    function it_formats_dates()
    {
        $dateFormats = [
            ['2000-01-05 20:00:00', 'H:i', '20:00'],
            [new DateTimeImmutable('2000-01-05 20:00:00'), 'Y-m-d', '2000-01-05'],
            ['today', 'Y-m-d', (new DateTimeImmutable('today'))->format('Y-m-d')],
        ];

        foreach ($dateFormats as [$date, $format, $formattedDate]) {
            $this::toDateFormat($date, $format)->shouldReturn($formattedDate);
            $this::toDateFormatOrNull($date, $format)->shouldReturn($formattedDate);
        }
    }

    function it_does_not_format_invalid_dates()
    {
        $notDates = ['xxx', false, null, ''];

        foreach ($notDates as $notDate) {
            $this::toDateFormatOrNull($notDate, 'Y')->shouldReturn(null);
            $this::shouldThrow(InvalidArgumentException::class)->during('toDateFormat', [$notDate, 'Y']);
        }
    }

    function it_returns_timestamp_from_date()
    {
        $dates = [
            'today 12:00',
            '2000-01-01',
            new DateTimeImmutable('2000-01-01'),
        ];

        foreach ($dates as $date) {
            $this::toTimestampOrNull($date)->shouldReturn(DateUtil::toDatetime($date)->getTimestamp());
        }
    }

    function it_assumes_that_integer_is_a_timestamp()
    {
        $timestamps = [
            123,
            '123'
        ];

        foreach ($timestamps as $timestamp) {
            $this::toTimestampOrNull($timestamp)->shouldReturn((int)$timestamp);
        }
    }

    function it_transforms_date_to_timestamp()
    {
        $date = new DateTimeImmutable('now');

        $this::toTimestampOrNull($date)->shouldReturn($date->getTimestamp());
    }
}
