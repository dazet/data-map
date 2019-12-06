<?php

namespace DataMap\Getter;

use DataMap\Common\NumberUtil;
use DataMap\Input\Input;

final class GetFloat implements Getter
{
    /** @var string */
    private $key;

    /** @var float|null */
    private $default;

    public function __construct(string $key, ?float $default = null)
    {
        $this->key = $key;
        $this->default = $default;
    }

    public function __invoke(Input $input): ?float
    {
        $value = $input->get($this->key);

        return NumberUtil::toFloatOrNull($value) ?? $this->default;
    }
}
