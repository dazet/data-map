<?php

namespace spec\DataMap\Pipe;

use DataMap\Pipe\Pipe;
use PhpSpec\ObjectBehavior;

final class PipelineSpec extends ObjectBehavior
{
    function it_defines_getter_key_and_value_transformation_as_stream_of_pipes()
    {
        $this->beConstructedWith('getter_key', new Pipe('trim'), new Pipe('strtoupper'));

        $this->key()->shouldBe('getter_key');
        $this->transform('    hello world    ')->shouldReturn('HELLO WORLD');
    }
}
