<?php

namespace DataMap\Getter;

use DataMap\Input\Input;

final class GetRaw implements Getter
{
    /** @var string */
    private $key;

    /** @var mixed */
    private $default;

    /**
     * @param mixed $default
     */
    public function __construct(string $key, $default = null)
    {
        $this->key = $key;
        $this->default = $default;
    }

    /**
     * @return mixed
     */
    public function __invoke(Input $input)
    {
        return $input->get($this->key, $this->default);
    }
}
