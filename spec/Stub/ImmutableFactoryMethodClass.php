<?php

namespace spec\DataMap\Stub;

final class ImmutableFactoryMethodClass
{
    /** @var mixed */
    private $one;

    /** @var mixed */
    private $two;

    /** @var mixed */
    private $three;

    private function __construct($one = null, $two = null, $three = null)
    {
        $this->one = $one;
        $this->two = $two;
        $this->three = $three;
    }

    public static function create($one = null, $two = null, $three = null): self
    {
        return new self($one, $two, $three);
    }

    public function getOne(): mixed
    {
        return $this->one;
    }

    public function getTwo(): mixed
    {
        return $this->two;
    }

    public function getThree(): mixed
    {
        return $this->three;
    }
}
