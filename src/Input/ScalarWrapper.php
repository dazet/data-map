<?php declare(strict_types=1);

namespace DataMap\Input;

/**
 * Scalar types cannot be treated as key-value structure by default.
 * In that case null will be returned for every key.
 */
final class ScalarWrapper implements Wrapper
{
    public function supportedTypes(): array
    {
        return ['null', 'string', 'integer', 'double', 'boolean'];
    }

    public function wrap($data): Input
    {
        return new NullInput();
    }
}
