<?php declare(strict_types=1);

namespace DataMap\Getter;

use DataMap\Input\Input;
use DateTimeImmutable;
use DateTimeZone;
use Dazet\TypeUtil\DateUtil;

final class GetDate implements Getter
{
    private string $key;

    private ?DateTimeImmutable $default;

    private ?DateTimeZone $timeZone;

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
