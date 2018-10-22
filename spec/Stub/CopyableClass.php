<?php

namespace spec\DataMap\Stub;

final class CopyableClass
{
    /** @var mixed */
    private $one;

    /** @var mixed */
    private $two;

    /** @var mixed */
    private $three;

    public function __construct($one = null, $two = null, $three = null)
    {
        $this->one = $one;
        $this->two = $two;
        $this->three = $three;
    }

    public function getOne()
    {
        return $this->one;
    }

    public function getTwo()
    {
        return $this->two;
    }

    public function getThree()
    {
        return $this->three;
    }

    public function withOne($one): self
    {
        $copy = clone $this;
        $copy->one = $one;

        return $copy;
    }

    public function withTwo($two): self
    {
        $copy = clone $this;
        $copy->two = $two;

        return $copy;
    }

    public function withThree($three): self
    {
        $copy = clone $this;
        $copy->three = $three;

        return $copy;
    }
}
