<?php declare(strict_types=1);

namespace DataMap\Getter;

use Dazet\TypeUtil\NumberUtil;
use DataMap\Input\Input;

final class GetInteger implements Getter
{
    private string $key;

    private ?int $default;

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
