<?php

namespace spec\DataMap\Input;

use DataMap\Input\ArrayInput;
use DataMap\Input\ExtensibleWrapper;
use DataMap\Input\RecursiveWrapper;
use DataMap\Pipe\Pipe;
use DataMap\Pipe\PipelineParser;
use PhpSpec\ObjectBehavior;

final class PipelineWrapperSpec extends ObjectBehavior
{
    function it_wraps_input_with_PipelineInput(ExtensibleWrapper $innerWrapper)
    {
        $this->beConstructedWith($innerWrapper);

        $data = ['text' => '  example  '];
        $innerWrapper->wrap($data)
            ->shouldBeCalled()
            ->willReturn(new ArrayInput($data));

        $input = $this->wrap($data);
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

    function it_can_be_created_with_custom_pipeline_parser()
    {
        $this->beConstructedWith(
            RecursiveWrapper::default(),
            new PipelineParser(['trim' => new Pipe('trim'), 'backward' => new Pipe('strrev')], false)
        );

        $data = ['nested' => ['value' => ' example ']];
        $this->wrap($data)->get('nested.value | trim | backward')->shouldReturn('elpmaxe');
    }
}
