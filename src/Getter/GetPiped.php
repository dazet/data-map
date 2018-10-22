<?php

namespace DataMap\Getter;

use DataMap\Input\Input;

/**
 * Get value from input and pass it through pipe functions
 */
final class GetPiped implements Getter
{
    /** @var string */
    private $key;

    /** @var callable[] */
    private $pipes;

    public function __construct(string $key, callable ...$pipes)
    {
        $this->key = $key;
        $this->pipes = $pipes;
    }

    /**
     * @return mixed
     */
    public function __invoke(Input $input)
    {
        $value = $input->get($this->key);

        foreach ($this->pipes as $pipe) {
            $value = $pipe($value);
        }

        return $value;
    }
}
