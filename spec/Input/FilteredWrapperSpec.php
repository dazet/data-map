<?php

namespace spec\DataMap\Input;

use DataMap\Input\ArrayInput;
use DataMap\Input\ExtensibleWrapper;
use DataMap\Input\FilteredInput;
use DataMap\Input\RecursiveWrapper;
use DataMap\Filter\Filter;
use DataMap\Filter\FilterChainParser;
use PhpSpec\ObjectBehavior;

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
            new FilterChainParser(['trim' => new Filter('trim'), 'backward' => new Filter('strrev')], false)
        );

        $data = ['nested' => ['value' => ' example ']];
        $this->wrap($data)->get('nested.value | trim | backward')->shouldReturn('elpmaxe');
    }
}
