<?php

namespace DataMap\Input;

use DataMap\Exception\FailedToWrapInput;
use DataMap\Pipe\Pipe;
use DataMap\Pipe\PipelineParser;

final class PipelineWrapper implements ExtensibleWrapper
{
    /** @var ExtensibleWrapper */
    private $inner;

    /** @var PipelineParser */
    private $parser;

    public function __construct(Wrapper $inner, ?PipelineParser $parser = null)
    {
        $this->inner = $inner instanceof ExtensibleWrapper ? $inner : new MixedWrapper($inner);
        $this->parser = $parser ?? PipelineParser::default();
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
        return new PipelineInput($this->inner->wrap($data), $this->parser);
    }

    public function withWrappers(Wrapper ...$wrappers): ExtensibleWrapper
    {
        $clone = clone $this;
        $clone->inner = $this->inner->withWrappers(...$wrappers);

        return $clone;
    }

    /**
     * @param Pipe[] $pipes
     */
    public function withPipes(array $pipes): self
    {
        $clone = clone $this;
        $clone->parser = $this->parser->withPipes($pipes);

        return $clone;
    }
}
