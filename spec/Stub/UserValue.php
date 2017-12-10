<?php

namespace spec\DataMap\Stub;

class UserValue implements UserInterface
{
    private $id;

    private $name;

    private $age;

    public function __construct($id, $name, $age)
    {
        $this->id = $id;
        $this->name = $name;
        $this->age = $age;
    }

    public function id()
    {
        return $this->id;
    }

    public function name()
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function age()
    {
        return $this->age;
    }
}
