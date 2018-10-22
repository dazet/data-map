<?php

namespace spec\DataMap\Input;

use DataMap\Input\Input;
use DataMap\Pipe\PipelineParser;
use PhpSpec\ObjectBehavior;

final class PipelineInputSpec extends ObjectBehavior
{
    function it_wraps_input_and_transforms_output_through_defined_pipeline(Input $input)
    {
        $this->beConstructedWith($input, PipelineParser::default());

        $input->has('data.path')->willReturn(true);
        $input->get('data.path')->shouldBeCalled()->willReturn('one two three');

        $this->has('data.path | string')->shouldBe(true);
        $this->get('data.path | string | explode " "')->shouldReturn(['one', 'two', 'three']);
        $this->get('data.path | string | upper')->shouldReturn('ONE TWO THREE');
    }
}
