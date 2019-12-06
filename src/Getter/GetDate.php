<?php

namespace DataMap\Getter;

use DataMap\Common\DateUtil;
use DataMap\Input\Input;
use DateTimeImmutable;
use DateTimeZone;

final class GetDate implements Getter
{
    /** @var string */
    private $key;

    /** @var DateTimeImmutable|null */
    private $default;

    /** @var DateTimeZone|null */
    private $timeZone;

    public function __construct(string $key, ?DateTimeImmutable $default = null, ?DateTimeZone $timeZone = null)
    {
        $this->key = $key;
        $this->default = $default;
        $this->timeZone = $timeZone;
    }

    public function __invoke(Input $input): ?DateTimeImmutable
    {
        $date = $input->get($this->key);

        return DateUtil::toDatetimeOrNull($date, $this->timeZone) ?? $this->default;
    }
}
