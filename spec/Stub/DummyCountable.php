<?php declare(strict_types=1);

namespace spec\DataMap\Stub;

use Countable;
use function random_int;

final class DummyCountable implements Countable
{
    /** @var int */
    private $count;

    public function __construct(?int $count = null)
    {
        $this->count = $count ?? random_int(0, 10000);
    }

    public function count(): int
    {
        return $this->count;
    }
}
