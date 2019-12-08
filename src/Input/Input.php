<?php declare(strict_types=1);

namespace DataMap\Input;

interface Input
{
    /**
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    public function has(string $key): bool;
}
