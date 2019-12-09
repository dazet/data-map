<?php declare(strict_types=1);

namespace DataMap\Getter;

use DataMap\Common\NumberUtil;
use DataMap\Input\Input;

final class GetInteger implements Getter
{
    /** @var string */
    private $key;

    /** @var int|null */
    private $default;

    public function __construct(string $key, ?int $default = null)
    {
        $this->key = $key;
        $this->default = $default;
    }

    public function __invoke(Input $input): ?int
    {
        $value = $input->get($this->key);

        return NumberUtil::toIntOrNull($value) ?? $this->default;
    }
}
