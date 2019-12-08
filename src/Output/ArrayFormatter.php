<?php declare(strict_types=1);

namespace DataMap\Output;

final class ArrayFormatter implements Formatter
{
    public static function default(): self
    {
        static $self;

        return $self ?? $self = new self();
    }

    /**
     * @param array<string, mixed> $output
     * @return array<string, mixed>
     */
    public function format(array $output): array
    {
        return $output;
    }
}
