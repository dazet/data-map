<?php

namespace DataMap\Input;

use DataMap\Exception\FailedToWrapInput;
use DataMap\Filter\Filter;
use DataMap\Filter\FilterChainParser;

final class FilteredWrapper implements ExtensibleWrapper
{
    /** @var ExtensibleWrapper */
    private $inner;

    /** @var FilterChainParser */
    private $parser;

    public function __construct(Wrapper $inner, ?FilterChainParser $parser = null)
    {
        $this->inner = $inner instanceof ExtensibleWrapper ? $inner : new MixedWrapper($inner);
        $this->parser = $parser ?? FilterChainParser::default();
    }

    public static function default(): self
    {
        return new self(RecursiveWrapper::default());
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
        return new FilteredInput($this->inner->wrap($data), $this->parser);
    }

    public function withWrappers(Wrapper ...$wrappers): ExtensibleWrapper
    {
        $clone = clone $this;
        $clone->inner = $this->inner->withWrappers(...$wrappers);

        return $clone;
    }

    /**
     * @param Filter[] $filters
     */
    public function withFilters(array $filters): self
    {
        $clone = clone $this;
        $clone->parser = $this->parser->withFilters($filters);

        return $clone;
    }
}
