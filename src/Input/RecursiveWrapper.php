<?php

namespace DataMap\Input;

use DataMap\Exception\FailedToWrapInput;

final class RecursiveWrapper implements Wrapper
{
    /** @var Wrapper */
    private $inner;

    public function __construct(Wrapper $inner)
    {
        $this->inner = $inner;
    }

    public static function default(): self
    {
        static $self;

        return $self ?? $self = new self(MixedWrapper::default());
    }

    public function supportedTypes(): array
    {
        return $this->inner->supportedTypes();
    }

    /**
     * @param mixed $data
     * @throws FailedToWrapInput
     */
    public function wrap($data): Input
    {
        return new RecursiveInput($this->inner->wrap($data), $this);
    }
}
