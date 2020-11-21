<?php declare(strict_types=1);

namespace DataMap\Getter;

use DataMap\Input\Input;
use function array_map;
use function array_merge;

final class GetMappedFlatCollection implements Getter
{
    private GetMappedCollection $getter;

    public function __construct(string $key, callable $mapper)
    {
        $this->getter = new GetMappedCollection($key, $mapper);
    }

    /**
     * @return mixed[]
     */
    public function __invoke(Input $input): array
    {
        return array_merge(...array_map('array_values', ($this->getter)($input)));
    }
}
