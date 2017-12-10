<?php

namespace DataMap\Getter;

use DataMap\Input\Input;

final class GetBoolean implements Getter
{
    public const TRUTH = [true, 1, '1'];
    public const FALSEHOOD = [false, 0, '0'];

    /** @var string */
    private $key;

    /** @var bool|null */
    private $default;

    public function __construct(string $key, ?bool $default = null)
    {
        $this->key = $key;
        $this->default = $default;
    }

    public function __invoke(Input $input): ?bool
    {
        $value = $input->get($this->key);

        if (\in_array($value, self::TRUTH, true)) {
            return true;
        }

        if (\in_array($value, self::FALSEHOOD, true)) {
            return false;
        }

        return $this->default;
    }
}
