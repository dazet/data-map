<?php declare(strict_types=1);

namespace DataMap\Getter;

use Dazet\TypeUtil\StringUtil;
use DataMap\Input\Input;

final class GetString implements Getter
{
    private string $key;

    private ?string $default;

    public function __construct(string $key, ?string $default = null)
    {
        $this->key = $key;
        $this->default = $default;
    }

    public function __invoke(Input $input): ?string
    {
        $value = $input->get($this->key);

        return StringUtil::toStringOrNull($value) ?? $this->default;
    }
}
