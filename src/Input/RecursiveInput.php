<?php

namespace DataMap\Input;

final class RecursiveInput implements Input
{
    /** @var Input */
    private $inner;

    /** @var Wrapper */
    private $wrapper;

    /** @var string */
    private $dot;

    public function __construct(Input $inner, Wrapper $wrapper, string $dot = '.')
    {
        if ($dot === '') {
            throw new \InvalidArgumentException('Dot cannot be empty string');
        }

        $this->inner = $inner;
        $this->wrapper = $wrapper;
        $this->dot = $dot;
    }

    /**
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if ($this->inner->has($key) || \strpos($key, $this->dot) === false) {
            return $this->inner->get($key, $default);
        }

        [$current, $rest] = $this->splitKey($key);
        $value = $this->inner->get($current);

        return $this->wrapValue($value)->get($rest, $default);
    }

    public function has(string $key): bool
    {
        if ($this->inner->has($key)) {
            return true;
        }

        if (\strpos($key, $this->dot) === false) {
            return false;
        }

        [$current, $rest] = $this->splitKey($key);
        $value = $this->inner->get($current);

        return $this->wrapValue($value)->has($rest);
    }

    private function splitKey(string $key): array
    {
        $position = \mb_strrpos($key, $this->dot);

        do {
            $current = \mb_substr($key, 0, $position);
            $rest = \mb_substr($key, $position + \strlen($this->dot));
            $position = \mb_strrpos($current, $this->dot);
        } while (!$this->inner->has($current) && $position !== false);

        return [$current, $rest];
    }

    private function wrapValue($value): Input
    {
        return new self($this->wrapper->wrap($value), $this->wrapper, $this->dot);
    }
}
