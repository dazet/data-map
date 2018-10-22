<?php

namespace spec\DataMap\Stub;

final class ImmutableClass
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
}
