<?php declare(strict_types=1);

namespace DataMap\Input;

final class NullInput implements Input
{
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $default;
    }

    public function has(string $key): bool
    {
        return false;
    }
}
