<?php

namespace DataMap\Input;

use DataMap\Exception\FailedToWrapInput;
use DataMap\Pipe\PipelineParser;

final class PipelineWrapper implements Wrapper
{
    /** @var Wrapper */
    private $inner;

    /** @var PipelineParser */
    private $parser;

    public function __construct(Wrapper $inner, ?PipelineParser $parser = null)
    {
        $this->inner = $inner;
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
}
