<?php

namespace spec\DataMap\Input;

use DataMap\Input\ArrayInput;
use DataMap\Input\MixedWrapper;
use PhpSpec\ObjectBehavior;

final class RecursiveInputSpec extends ObjectBehavior
{
    private const FOO_DATA = [
        'name' => 'John Foo',
        'mother' => [
            'name' => 'Mary Foo',
            'mother' => [
                'name' => 'Linda Bar',
            ],
            'father' => [
                'name' => 'James Bar',
            ],
        ],
        'father' => [
            'name' => 'unknown',
        ],
    ];

    function let()
    {
        $this->beConstructedWith(new ArrayInput(self::FOO_DATA), MixedWrapper::default());
    }

    function it_fetches_value_from_inner_input()
    {
        $this->get('name')->shouldBe('John Foo');
    }

    function it_fetches_value_from_inner_input_recursively_using_dot_notation()
    {
        $this->get('mother.name')->shouldBe('Mary Foo');
        $this->get('mother.mother.name')->shouldBe('Linda Bar');

        $this->get('father.name')->shouldBe('unknown');
        $this->get('father.father.name')->shouldBe(null);
    }

    function it_fetches_value_from_inner_input_recursively_using_custom_notation()
    {
        $this->beConstructedWith(new ArrayInput(self::FOO_DATA), MixedWrapper::default(), '->');

        $this->get('mother->name')->shouldBe('Mary Foo');
        $this->get('mother->mother->name')->shouldBe('Linda Bar');

        $this->get('father->name')->shouldBe('unknown');
        $this->get('father->father->name')->shouldBe(null);
    }

    function it_checks_existence_in_inner_input()
    {
        $this->has('name')->shouldBe(true);
    }

    function it_check_existence_in_inner_input_recursively_using_dot_notation()
    {
        $this->has('mother.name')->shouldBe(true);
        $this->has('mother.mother.name')->shouldBe(true);
        $this->has('father.name')->shouldBe(true);
        $this->has('father.father.name')->shouldBe(false);
    }

    function it_checks_existence_in_inner_input_recursively_using_custom_notation()
    {
        $this->beConstructedWith(new ArrayInput(self::FOO_DATA), MixedWrapper::default(), '->');

        $this->has('mother->name')->shouldBe(true);
        $this->has('mother->mother->name')->shouldBe(true);

        $this->has('father->name')->shouldBe(true);
        $this->has('father->father->name')->shouldBe(false);
    }
}
