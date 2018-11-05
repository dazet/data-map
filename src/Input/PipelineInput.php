<?php

namespace DataMap\Input;

use DataMap\Pipe\PipelineParser;

final class PipelineInput implements Input
{
    /** @var Input */
    private $inner;

    /** @var PipelineParser */
    private $parser;

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
        $pipeline = $this->parser->parse($key);
        $value = $this->inner->get($pipeline->key());

        return $pipeline->transform($value);
    }

    public function has(string $key): bool
    {
        return $this->inner->has($this->parser->parse($key)->key());
    }
}
