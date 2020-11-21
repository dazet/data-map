<?php declare(strict_types=1);

namespace DataMap\Getter;

use Dazet\TypeUtil\NumberUtil;
use DataMap\Input\Input;

final class GetFloat implements Getter
{
    private string $key;

    private ?float $default;

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
