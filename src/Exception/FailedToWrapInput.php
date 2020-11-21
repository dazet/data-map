<?php declare(strict_types=1);

namespace DataMap\Exception;

use RuntimeException;
use Throwable;

final class FailedToWrapInput extends RuntimeException implements MapperException
{
    public function __construct(string $message, Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
