<?php

namespace spec\DataMap\Getter;

use DataMap\Input\ArrayInput;
use PhpSpec\ObjectBehavior;

final class GetDateSpec extends ObjectBehavior
{
    function it_maps_time_string_to_DateTimeImmutable()
    {
        $this->beConstructedWith('date');

        $date = '1999-12-31 23:59:59';

        $this(new ArrayInput(['date' => $date]))->shouldBeLike(new \DateTimeImmutable($date));
    }

    function it_maps_DateTime_to_DateTimeImmutable()
    {
        $this->beConstructedWith('date');

        $date = new \DateTime('yesterday');

        $this(new ArrayInput(['date' => $date]))->shouldBeLike(\DateTimeImmutable::createFromMutable($date));
    }

    function it_returns_DateTimeImmutable()
    {
        $this->beConstructedWith('date');

        $date = new \DateTimeImmutable('yesterday');

        $this(new ArrayInput(['date' => $date]))->shouldBe($date);
    }

    function it_returns_null_by_default_when_input_is_not_valid_time_string_or_null()
    {
        $this->beConstructedWith('date');

        $this(new ArrayInput(['date' => 'qwerty']))->shouldBe(null);
    }

    function it_can_return_default_value_when_input_is_not_valid_time_string_or_null()
    {
        $default = new \DateTimeImmutable();
        $this->beConstructedWith('date', $default);

        $this(new ArrayInput(['date' => 'qwerty']))->shouldBe($default);
        $this(new ArrayInput([]))->shouldBe($default);
    }

    function it_can_create_date_with_requested_timezone()
    {
        $timezone = new \DateTimeZone('Pacific/Easter');
        $this->beConstructedWith('date', null, $timezone);

        $date = '1999-12-31 23:59:59';

        $this(new ArrayInput(['date' => $date]))->shouldBeLike(new \DateTimeImmutable($date, $timezone));
    }
}
