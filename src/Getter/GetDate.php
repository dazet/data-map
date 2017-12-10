<?php

namespace DataMap\Getter;

use DataMap\Input\Input;

final class GetDate implements Getter
{
    /** @var string */
    private $key;

    /** @var \DateTimeImmutable|null */
    private $default;

    /** @var \DateTimeZone|null */
    private $timeZone;

    public function __construct(string $key, ?\DateTimeImmutable $default = null, ?\DateTimeZone $timeZone = null)
    {
        $this->key = $key;
        $this->default = $default;
        $this->timeZone = $timeZone;
    }

    public function __invoke(Input $input): ?\DateTimeImmutable
    {
        $date = $input->get($this->key);

        if ($date instanceof \DateTimeImmutable) {
            return $date;
        }

        if ($date instanceof \DateTime) {
            return \DateTimeImmutable::createFromMutable($date);
        }

        if ($date === null || \strtotime($date) === false) {
            return $this->default;
        }

        return new \DateTimeImmutable($date, $this->timeZone);
    }
}
