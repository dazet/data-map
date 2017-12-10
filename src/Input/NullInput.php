<?php

namespace DataMap\Input;

final class NullInput implements Input
{
    public function get(string $key, $default = null)
    {
        return $default;
    }

    public function has(string $key): bool
    {
        return false;
    }
}
