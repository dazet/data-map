<?php declare(strict_types=1);

namespace DataMap\Input;

use ArrayAccess;
use DataMap\Exception\FailedToWrapInput;

final class ArrayWrapper implements Wrapper
{
    public function supportedTypes(): array
    {
        return ['array', ArrayAccess::class];
    }

    /**
     * @param array<string, mixed>|ArrayAccess<string, mixed> $data
     * @throws FailedToWrapInput
     */
    public function wrap($data): Input
    {
        return new ArrayInput($data);
    }
}
