<?php

namespace DataMap\Pipe;

final class Pipeline
{
    /** @var string */
    private $key;

    /** @var Pipe[] */
    private $pipes;

    public function __construct(string $key, Pipe ...$pipes)
    {
        $this->key = $key;
        $this->pipes = $pipes;
    }

    public function key(): string
    {
        return $this->key;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function transform($value)
    {
        $result = $value;
        foreach ($this->pipes as $pipe) {
            $result = $pipe($result);
        }

        return $result;
    }
}
