<?php

namespace spec\DataMap\Getter;

use DataMap\Getter\GetInteger;
use DataMap\Getter\GetRaw;
use DataMap\Getter\GetterMap;
use PhpSpec\ObjectBehavior;

final class GetterMapSpec extends ObjectBehavior
{
    function it_contains_key_getter_association()
    {
        $map = ['a' => new GetInteger('x'), 'b' => new GetRaw('y')];
        $this->beConstructedWith($map);

        $this->getIterator()->shouldIterateAs(new \ArrayIterator($map));
    }

    function it_wraps_with_GetRaw_getter_defined_by_string()
    {
        $this->beConstructedWith(['a' => 'x', 'b' => 'y']);

        $this->getIterator()->shouldIterateLike(new \ArrayIterator(['a' => new GetRaw('x'), 'b' => new GetRaw('y')]));
    }

    function it_can_be_merged_with_other_map()
    {
        $map1 = ['a' => new GetRaw('x'), 'b' => new GetRaw('y')];
        $map2 = ['b' => new GetRaw('y2'), 'c' => new GetRaw('z')];
        $this->beConstructedWith($map1);

        $other = new GetterMap($map2);
        $merged = $this->merge($other);

        $merged->shouldBeLike(new GetterMap(['a' => $map1['a'], 'b' => $map2['b'], 'c' => $map2['c']]));

        $merged->shouldNotBe($this);
        $merged->shouldNotBe($other);
    }
}
