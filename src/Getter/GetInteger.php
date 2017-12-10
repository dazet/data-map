<?php

namespace DataMap\Getter;

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

        return \is_numeric($value) ? (int)$value : $this->default;
    }
}
