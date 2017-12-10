<?php

namespace DataMap\Output;

final class ArrayFormatter implements Formatter
{
    public static function default(): self
    {
        static $self;

        return $self ?? $self = new self();
    }

    public function format(array $output)
    {
        return $output;
    }
}
