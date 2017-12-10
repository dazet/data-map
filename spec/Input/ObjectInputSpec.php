<?php

namespace spec\DataMap\Input;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\DataMap\Stub\UserDto;
use spec\DataMap\Stub\UserValue;

final class ObjectInputSpec extends ObjectBehavior
{
    function it_can_get_data_from_public_properties()
    {
        $user = new UserDto();
        $user->id = 'abc-123';
        $user->name = 'John Doe';

        $this->beConstructedWith($user);

        $this->get('id')->shouldBe('abc-123');
        $this->get('name')->shouldBe('John Doe');
        $this->get('age')->shouldBe(null);
    }

    function it_can_get_data_from_public_properties_of_anonymous_class()
    {
        $user = new class
        {
            public $id = 'abc-123';

            public $name = 'John Doe';
        };

        $this->beConstructedWith($user);

        $this->get('id')->shouldBe('abc-123');
        $this->get('name')->shouldBe('John Doe');
        $this->get('age')->shouldBe(null);
    }

    function it_can_get_data_from_getter_methods()
    {
        $user = new UserValue('abc-123', 'John Doe', 23);

        $this->beConstructedWith($user);

        $this->get('id')->shouldBe('abc-123');
        $this->get('name')->shouldBe('John Doe');
        $this->get('age')->shouldBe(23);
        $this->get('zonk')->shouldBe(null);
    }

    function it_can_get_data_from_anonymous_class_getter_methods()
    {
        $user = new class
        {
            public function id(): string
            {
                return 'abc-123';
            }

            public function name(): string
            {
                return 'John Doe';
            }
        };

        $this->beConstructedWith($user);

        $this->get('id')->shouldBe('abc-123');
        $this->get('name')->shouldBe('John Doe');
        $this->get('age')->shouldBe(null);
    }

    function it_tries_to_resolve_popular_getter_prefixes()
    {
        $user = new class
        {
            public function getId(): string
            {
                return 'abc-123';
            }

            public function getName(): string
            {
                return 'John Doe';
            }

            public function isOld(): bool
            {
                return false;
            }
        };

        $this->beConstructedWith($user);

        $this->get('id')->shouldBe('abc-123');
        $this->get('name')->shouldBe('John Doe');
        $this->get('old')->shouldBe(false);
    }

    function it_does_not_try_to_call_methods_that_have_arguments(UserValue $user)
    {
        $this->beConstructedWith($user);

        $user->id()->willReturn('abc-123');
        $user->name()->willReturn('John Doe');
        $user->setName(Argument::any())->shouldNotBeCalled();

        $this->get('id')->shouldBe('abc-123');
        $this->get('setName')->shouldBe(null);
        $this->get('name')->shouldBe('John Doe');
    }
}
