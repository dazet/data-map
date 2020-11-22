<?php declare(strict_types=1);

namespace spec\DataMap\Filter;

use DataMap\Filter\Filter;
use Psr\Container\ContainerInterface;
use function in_array;

final class DummyFilterContainer implements ContainerInterface
{
    /** @var string[] */
    private array $allowedFilters = [];

    public function __construct(string ...$allowedFilters)
    {
        $this->allowedFilters = $allowedFilters;
    }

    public function get($id): Filter
    {
        return new Filter(
            function (string $value) use ($id): string {
                return "{$value} filtered by {$id}";
            }
        );
    }

    public function has($id): bool
    {
        return in_array($id, $this->allowedFilters, true);
    }
}
