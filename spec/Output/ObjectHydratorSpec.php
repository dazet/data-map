<?php

namespace spec\DataMap\Output;

use InvalidArgumentException;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;
use spec\DataMap\Stub\AnemicClass;
use spec\DataMap\Stub\CopyableClass;
use spec\DataMap\Stub\UserDto;

final class ObjectHydratorSpec extends ObjectBehavior
{
    function it_hydrates_public_properties()
    {
        $this->beConstructedWith(new UserDto());

        $expected = new UserDto();
        $expected->id = 'abc-123';
        $expected->name = 'John Doe';
        $expected->age = 55;

        $this->format(['id' => 'abc-123', 'name' => 'John Doe', 'age' => 55])->shouldBeLike($expected);
    }

    function it_does_not_change_initial_object()
    {
        $initial = new UserDto();
        $initial->id = 1;
        $initial->name = 'abc';
        $initial->age = 12;

        $this->beConstructedWith($initial);

        $this->format(['id' => 'abc-123', 'name' => 'John Doe', 'age' => 55])->shouldNotBeLike($initial);

        if ($initial->id !== 1 || $initial->name !== 'abc' || $initial->age !== 12) {
            throw new FailureException('Hydration initial object should not have changed.');
        }
    }

    function it_keeps_initial_object_immutable()
    {
        $initial = new UserDto();
        $initial->name = 'John Doe';

        $this->beConstructedWith($initial);

        $this->format([])->name->shouldBe('John Doe');
        $initial->name = 'Mary Doe';
        $this->format([])->name->shouldBe('John Doe');
    }

    function it_creates_simple_object_when_passed_by_class_reference()
    {
        $this->beConstructedWith(UserDto::class);

        $expected = new UserDto();
        $expected->id = 'abc-123';
        $expected->name = 'John Doe';
        $expected->age = 55;

        $this->format(['id' => 'abc-123', 'name' => 'John Doe', 'age' => 55])->shouldBeLike($expected);
    }

    function it_hydrates_object_through_setters()
    {
        $this->beConstructedWith(new AnemicClass());

        $this
            ->format(['one' => 'value 1', 'two' => 'value 2', 'three' => 'value 3'])
            ->shouldBeLike(new AnemicClass('value 1', 'value 2', 'value 3'));
    }

    function it_hydrates_immutable_object_with_modifiers()
    {
        $this->beConstructedWith(new CopyableClass());

        $this
            ->format(['one' => 'value 1', 'two' => 'value 2', 'three' => 'value 3'])
            ->shouldBeLike(new CopyableClass('value 1', 'value 2', 'value 3'));
    }

    function it_throws_InvalidArgumentException_when_constructed_with_array()
    {
        $this->beConstructedWith([]);
        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }

    function it_throws_InvalidArgumentException_when_constructed_with_null()
    {
        $this->beConstructedWith(null);
        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }

    function it_throws_InvalidArgumentException_when_constructed_with_empty_string()
    {
        $this->beConstructedWith('');
        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }
}
