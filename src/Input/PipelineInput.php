<?php

namespace DataMap\Input;

use DataMap\Pipe\Pipeline;
use DataMap\Pipe\PipelineParser;

final class PipelineInput implements Input
{
    /** @var Input */
    private $inner;

    /** @var PipelineParser */
    private $parser;

    /** @var Pipeline[] */
    private $parsed = [];

    public function __construct(Input $inner, PipelineParser $parser)
    {
        $this->inner = $inner;
        $this->parser = $parser;
    }

    /**
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $pipeline = $this->parse($key);

        return $pipeline->transform($this->inner->get($pipeline->key()));
    }

    public function has(string $key): bool
    {
        return $this->inner->has($this->parse($key)->key());
    }

    private function parse(string $key): Pipeline
    {
        return $this->parsed[$key] ?? $this->parsed[$key] = $this->parser->parse($key);
    }
}
