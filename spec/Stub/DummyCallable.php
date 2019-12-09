<?php

namespace spec\DataMap\Stub;

class DummyCallable
{
    /** @var mixed */
    private $return;

    public function __construct($return = null)
    {
        $this->return = $return;
    }

    public function __invoke(...$args)
    {
        return $this->return;
    }
}
