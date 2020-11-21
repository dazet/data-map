<?php declare(strict_types=1);

namespace DataMap\Input;

use InvalidArgumentException;
use function mb_strrpos;
use function mb_substr;
use function strlen;

final class RecursiveInput implements Input
{
    private Input $inner;

    private Wrapper $wrapper;

    private string $dot;

    public function __construct(Input $inner, Wrapper $wrapper, string $dot = '.')
    {
        if ($dot === '') {
            throw new InvalidArgumentException('Dot cannot be empty string');
        }

        $this->inner = $inner;
        $this->wrapper = $wrapper;
        $this->dot = $dot;
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if ($this->inner->has($key)) {
            return $this->inner->get($key, $default);
        }

        [$current, $rest] = $this->splitKey($key);
        $value = $this->inner->get($current);

        return $rest !== null ? $this->wrapValue($value)->get($rest, $default) : $value;
    }

    public function has(string $key): bool
    {
        if ($this->inner->has($key)) {
            return true;
        }

        [$current, $rest] = $this->splitKey($key);

        if ($rest === null) {
            return false;
        }

        $value = $this->inner->get($current);

        return $this->wrapValue($value)->has($rest);
    }

    /**
     * @return array{0: string, 1: string|null}
     */
    private function splitKey(string $key): array
    {
        $position = mb_strrpos($key, $this->dot);

        if ($position === false) {
            return [$key, null];
        }

        do {
            $current = mb_substr($key, 0, $position);
            $rest = mb_substr($key, $position + strlen($this->dot));
            $position = mb_strrpos($current, $this->dot);
        } while (!$this->inner->has($current) && $position !== false);

        return [$current, $rest];
    }

    /**
     * @param mixed $value
     */
    private function wrapValue($value): Input
    {
        return new self($this->wrapper->wrap($value), $this->wrapper, $this->dot);
    }
}
