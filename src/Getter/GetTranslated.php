<?php

namespace DataMap\Getter;

use DataMap\Input\Input;

final class GetTranslated implements Getter
{
    /** @var string */
    private $key;

    /** @var array */
    private $map;

    /** @var mixed */
    private $default;

    public function __construct(string $key, array $map, $default = null)
    {
        $this->key = $key;
        $this->map = $map;
        $this->default = $default;
    }

    public function __invoke(Input $input)
    {
        $value = $input->get($this->key);

        if (!\is_scalar($value)) {
            return $this->default;
        }

        return $this->map[$value] ?? $this->default;
    }
}
