<?php

namespace DataMap\Common;

final class VariableUtil
{
    private function __construct()
    {
    }

    public static function ifEmpty($value, $default)
    {
        return empty($value) ? $default : $value;
    }

    public static function ifNull($value, $default)
    {
        return $value ?? $default;
    }
}
