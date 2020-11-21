<?php declare(strict_types=1);

namespace DataMap\Getter;

use DataMap\Input\Input;
use function is_scalar;

final class GetTranslated implements Getter
{
    private string $key;

    /** @var array<string|int, mixed> */
    private array $map;

    /** @var mixed */
    private $default;

    /**
     * @param array<string|int, mixed> $map
     * @param mixed $default
     */
    public function __construct(string $key, array $map, $default = null)
    {
        $this->key = $key;
        $this->map = $map;
        $this->default = $default;
    }

    /**
     * @return mixed
     */
    public function __invoke(Input $input)
    {
        $value = $input->get($this->key);

        if (!is_scalar($value)) {
            return $this->default;
        }

        return $this->map[$value] ?? $this->default;
    }
}
