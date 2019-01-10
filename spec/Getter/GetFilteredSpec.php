<?php

namespace spec\DataMap\Getter;

use DataMap\Input\Input;
use DataMap\Filter\Filter;
use PhpSpec\ObjectBehavior;

final class GetFilteredSpec extends ObjectBehavior
{
    function it_gets_value_by_key_and_transforms_it_with_provided_callbacks(Input $input)
    {
        $this->beConstructedWith('get_by_key', 'trim', 'strtoupper');
        $input->get('get_by_key')->willReturn(' this is example ');

        $this($input)->shouldReturn('THIS IS EXAMPLE');
    }

    function it_can_make_use_of_Filter_definition(Input $input)
    {
        $this->beConstructedWith('key', 'trim', 'strtoupper', new Filter('explode', [' ', '$$']));
        $input->get('key')->willReturn(' this is example ');

        $this($input)->shouldReturn(['THIS', 'IS', 'EXAMPLE']);
    }
}
