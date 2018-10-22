<?php

namespace DataMap\Common;

final class JsonUtil
{
    private function __construct()
    {
    }

    public static function toJsonOrNull($value): ?string
    {
        $json = \json_encode($value, JSON_UNESCAPED_UNICODE);

        return $json !== false ? $json : null;
    }

    public static function toArrayOrNull($value): ?array
    {
        if (!\is_string($value)) {
            return null;
        }

        $array = \json_decode($value, true);

        return \is_array($array) ? $array : null;
    }
}
