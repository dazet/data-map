<?php

namespace DataMap\Input;

use DataMap\Exception\FailedToWrapInput;

final class RecursiveWrapper implements ExtensibleWrapper
{
    /** @var ExtensibleWrapper */
    private $inner;

    public function __construct(Wrapper $inner)
    {
        $this->inner = $inner instanceof ExtensibleWrapper ? $inner : new MixedWrapper($inner);
    }

    public static function default(): self
    {
        static $default;

        return $default ?? $default = new self(MixedWrapper::default());
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

    public function withWrappers(Wrapper ...$wrappers): ExtensibleWrapper
    {
        $clone = clone $this;
        $clone->inner = $this->inner->withWrappers(...$wrappers);

        return $clone;
    }
}
