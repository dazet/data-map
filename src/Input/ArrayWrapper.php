<?php

namespace DataMap\Input;

use DataMap\Exception\FailedToWrapInput;

final class ArrayWrapper implements Wrapper
{
    public function supportedTypes(): array
    {
        return ['array', \ArrayAccess::class];
    }

    /**
     * @param array|\ArrayAccess $data
     * @throws FailedToWrapInput
     */
    public function wrap($data): Input
    {
        return new ArrayInput($data);
    }
}
