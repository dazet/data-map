<?php

namespace DataMap\Getter;

use DataMap\Input\Input;
use function array_filter;
use function array_map;
use function implode;

final class GetJoinedStrings implements Getter
{
    /** @var string */
    private $glue;

    /** @var GetString[] */
    private $getters;

    public function __construct(string $glue, string ...$keys)
    {
        $this->glue = $glue;
        $this->getters = array_map(
            function (string $key): GetString {
                return new GetString($key);
            },
            $keys
        );
    }

    public function __invoke(Input $input): string
    {
        return implode(
            $this->glue,
            array_filter(
                array_map(
                    function (GetString $get) use ($input): ?string {
                        return $get($input);
                    },
                    $this->getters
                )
            )
        );
    }
}
