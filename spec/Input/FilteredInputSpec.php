<?php

namespace spec\DataMap\Input;

use DataMap\Input\Input;
use DataMap\Filter\InputFilterParser;
use PhpSpec\ObjectBehavior;

final class FilteredInputSpec extends ObjectBehavior
{
    function it_wraps_input_and_transforms_output_through_defined_filter_chain(Input $input)
    {
        $this->beConstructedWith($input, InputFilterParser::default());

        $input->has('data.path')->willReturn(true);
        $input->get('data.path')->shouldBeCalled()->willReturn('one two three');

        $this->has('data.path | string')->shouldBe(true);
        $this->get('data.path | string | explode " "')->shouldReturn(['one', 'two', 'three']);
        $this->get('data.path | string | upper')->shouldReturn('ONE TWO THREE');
    }
}
