<?php

namespace DataMap\Input;

interface Input
{
    /**
     * @return mixed
     */
    public function get(string $key, $default = null);

    public function has(string $key): bool;
}
