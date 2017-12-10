<?php

namespace DataMap\Input;

use DataMap\Exception\FailedToWrapInput;

final class ObjectWrapper implements Wrapper
{
    public function supportedTypes(): array
    {
        return ['object'];
    }

    /**
     * @param object $data
     * @throws FailedToWrapInput
     */
    public function wrap($data): Input
    {
        return new ObjectInput($data);
    }
}
