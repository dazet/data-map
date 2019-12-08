<?php declare(strict_types=1);

namespace DataMap\Input;

use ArrayAccess;
use DataMap\Exception\FailedToWrapInput;
use function array_key_exists;
use function is_array;

final class ArrayInput implements Input
{
    /** @var array<string, mixed>|ArrayAccess<string, mixed> */
    private $array;

    /**
     * @param array<string, mixed>|ArrayAccess<string, mixed>|mixed $data
     * @throws FailedToWrapInput
     */
    public function __construct($data)
    {
        if (!is_array($data) && !($data instanceof ArrayAccess)) {
            throw new FailedToWrapInput('ArrayInput can only wrap array or ArrayAccess');
        }

        $this->array = $data;
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->array[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        if ($this->array instanceof ArrayAccess) {
            return $this->array->offsetExists($key);
        }

        return array_key_exists($key, $this->array);
    }
}
