<?php

namespace spec\DataMap\Input;

use DataMap\Exception\FailedToWrapInput;
use DataMap\Filter\Filter;
use DataMap\Filter\InputFilterParser;
use DataMap\Input\ArrayInput;
use DataMap\Input\ArrayWrapper;
use DataMap\Input\ExtensibleWrapper;
use DataMap\Input\FilteredInput;
use DataMap\Input\MixedWrapper;
use DataMap\Input\ObjectWrapper;
use DataMap\Input\RecursiveWrapper;
use DataMap\Input\Wrapper;
use PhpSpec\ObjectBehavior;
use spec\PhpSpec\Wrapper\Subject\ExampleClass;
use function array_merge;

final class FilteredWrapperSpec extends ObjectBehavior
{
    function it_wraps_input_with_FilteredInput(ExtensibleWrapper $innerWrapper)
    {
        $this->beConstructedWith($innerWrapper);

        $data = ['text' => '  example  '];
        $innerWrapper->wrap($data)
            ->shouldBeCalled()
            ->willReturn(new ArrayInput($data));

        $input = $this->wrap($data);
        $input->shouldHaveType(FilteredInput::class);
        $input->get('text | trim')->shouldReturn('example');
    }

    function it_can_be_created_with_default_inner_recursive_wrapper()
    {
        $this->beConstructedThrough('default');

        $data = [
            'nested' => [
                'content' => [
                    'value' => '  example  ',
                ],
            ],
        ];

        $this->wrap($data)->get('nested.content.value | trim')->shouldReturn('example');
    }

    function it_can_be_created_with_custom_filter_chain_parser()
    {
        $this->beConstructedWith(
            RecursiveWrapper::default(),
            new InputFilterParser(['trim' => new Filter('trim'), 'backward' => new Filter('strrev')], false)
        );

        $data = ['nested' => ['value' => ' example ']];
        $this->wrap($data)->get('nested.value | trim | backward')->shouldReturn('elpmaxe');
    }

    function it_supports_type_of_inner_wrapper(Wrapper $inner)
    {
        $this->beConstructedWith($inner);
        $inner->supportedTypes()->willReturn([ExampleClass::class, 'object']);
        $this->supportedTypes()->shouldReturn([ExampleClass::class, 'object']);
    }

    function it_can_be_extended_with_wrappers()
    {
        $inner1 = new ObjectWrapper();
        $this->beConstructedWith($inner1);
        $object = new \stdClass();
        $object->property = ' object text ';
        $array = ['index' => ' array text '];

        $this->supportedTypes()->shouldBe($inner1->supportedTypes());
        $this->wrap($object)->get('property | trim')->shouldBe('object text');
        $this->shouldThrow(FailedToWrapInput::class)->during('wrap', [$array]);

        $inner2 = new ArrayWrapper();
        $extended = $this->withWrappers($inner2);
        $extended->supportedTypes()->shouldBe(array_merge($inner1->supportedTypes(), $inner2->supportedTypes()));
        $extended->wrap($array)->get('index | trim')->shouldBe('array text');
    }

    function it_can_be_extended_with_custom_filters()
    {
        $this->beConstructedWith(MixedWrapper::default());

        $wrapper = $this->withFilters([
            'prefix' => new Filter(
                static function (string $s, string $prefix): string {
                    return "{$prefix}{$s}";
                }
            ),
            'postfix' => new Filter(
                static function (string $s, string $postfix): string {
                    return "{$s}{$postfix}";
                }
            ),
        ]);

        $input = $wrapper->wrap(['name' => 'John']);

        $input->get('name | prefix "Hi "')->shouldBe('Hi John');
        $input->get('name | prefix "Hi " | postfix ", how are you?"')->shouldBe('Hi John, how are you?');
    }
}
