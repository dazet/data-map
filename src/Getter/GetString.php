<?php

namespace DataMap\Getter;

use DataMap\Input\Input;

final class GetString implements Getter
{
    /** @var string */
    private $key;

    /** @var string|null */
    private $default;

    public function __construct(string $key, ?string $default = null)
    {
        $this->key = $key;
        $this->default = $default;
    }

    public function __invoke(Input $input): ?string
    {
        $value = $input->get($this->key);

        return $this->canBeString($value) ? (string)$value : $this->default;
    }

    private function canBeString($value): bool
    {
        return \is_scalar($value) || (\is_object($value) && \method_exists($value, '__toString'));
    }
}
