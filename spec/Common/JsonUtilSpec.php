<?php

namespace spec\DataMap\Common;

use PhpSpec\ObjectBehavior;
use function fclose;
use function tmpfile;

final class JsonUtilSpec extends ObjectBehavior
{
    function it_encodes_json_when_possible()
    {
        $this::toJsonOrNull(['key' => 'value'])->shouldBe('{"key":"value"}');
        $this::toJsonOrNull(null)->shouldBe('null');
    }

    function it_returns_null_when_cannot_encode_json()
    {
        $tmp = tmpfile();
        $this::toJsonOrNull($tmp)->shouldBe(null);
        fclose($tmp);
    }

    function it_can_decode_json_to_array_when_possible()
    {
        $this::toArrayOrNull('{"key":"value"}')->shouldBe(['key' => 'value']);
    }

    function it_returns_null_when_cannot_decode_json()
    {
        $this::toArrayOrNull('{"key":"value"')->shouldBe(null);
    }

    function it_returns_null_when_not_string()
    {
        $this::toArrayOrNull([])->shouldBe(null);
    }

}
