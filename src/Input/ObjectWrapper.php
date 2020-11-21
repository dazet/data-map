<?php declare(strict_types=1);

namespace DataMap\Input;

use DataMap\Exception\FailedToWrapInput;
use function is_object;

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
        if (!is_object($data)) {
            throw new FailedToWrapInput('ObjectInput can only wrap object');
        }

        return new ObjectInput($data);
    }
}
