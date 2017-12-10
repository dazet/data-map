<?php

namespace DataMap\Input;

use DataMap\Exception\FailedToWrapInput;

final class ArrayInput implements Input
{
    /** @var array|\ArrayAccess */
    private $array;

    /**
     * @throws FailedToWrapInput
     */
    public function __construct($data)
    {
        if (!\is_array($data) && !$data instanceof \ArrayAccess) {
            throw new FailedToWrapInput('ArrayInput can only wrap array or ArrayAccess');
        }

        $this->array = $data;
    }

    /**
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->array[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->array);
    }
}
