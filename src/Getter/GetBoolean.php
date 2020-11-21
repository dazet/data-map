<?php declare(strict_types=1);

namespace DataMap\Getter;

use Dazet\TypeUtil\BooleanUtil;
use DataMap\Input\Input;

final class GetBoolean implements Getter
{
    private string $key;

    private ?bool $default;

    public function __construct(string $key, ?bool $default = null)
    {
        $this->key = $key;
        $this->default = $default;
    }

    public function __invoke(Input $input): ?bool
    {
        $value = $input->get($this->key);

        return BooleanUtil::toBoolOrNull($value) ?? $this->default;
    }
}
