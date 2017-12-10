<?php

namespace DataMap\Input;

use DataMap\Exception;

interface Wrapper
{
    /**
     * @return string[]
     */
    public function supportedTypes(): array;

    /**
     * @param mixed $data
     * @throws Exception\FailedToWrapInput
     */
    public function wrap($data): Input;
}
