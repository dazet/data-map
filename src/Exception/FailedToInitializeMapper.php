<?php

namespace DataMap\Exception;

final class FailedToInitializeMapper extends \RuntimeException implements MapperException
{
    public function __construct(string $message, \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
