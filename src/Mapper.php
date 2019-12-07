<?php

namespace DataMap;

use DataMap\Getter\GetterMap;
use DataMap\Input\FilteredWrapper;
use DataMap\Input\Wrapper;
use DataMap\Output\ArrayFormatter;
use DataMap\Output\Formatter;

final class Mapper
{
    /**
     * Defines getter for given key of output structure.
     * @var GetterMap
     */
    private $map;

    /**
     * Wraps input structure with Input object.
     * @var Wrapper
     */
    private $wrapper;

    /**
     * Formats output.
     * @var Formatter
     */
    private $formatter;

    /**
     * @param iterable<string, callable|string> $map
     */
    public function __construct(iterable $map, ?Formatter $formatter = null, ?Wrapper $wrapper = null)
    {
        $this->map = GetterMap::fromIterable($map);
        $this->wrapper = $wrapper ?? FilteredWrapper::default();
        $this->formatter = $formatter ?? ArrayFormatter::default();
    }

    /**
     * @param mixed $input Input supported by Wrapper.
     * @return mixed Output type depends on Formatter.
     */
    public function map($input)
    {
        $wrapped = $this->wrapper->wrap($input);

        $output = [];
        foreach ($this->map as $key => $getter) {
            $output[$key] = $getter($wrapped, $input);
        }

        return $this->formatter->format($output);
    }

    /**
     * @param mixed $input
     * @return mixed
     */
    public function __invoke($input)
    {
        return $this->map($input);
    }

    public function withWrapper(Wrapper $wrapper): self
    {
        $clone = clone $this;
        $clone->wrapper = $wrapper;

        return $clone;
    }

    public function withFormatter(Formatter $formatter): self
    {
        $clone = clone $this;
        $clone->formatter = $formatter;

        return $clone;
    }

    public function withGetters(GetterMap $getterMap): self
    {
        $clone = clone $this;
        $clone->map = $this->map->merge($getterMap);

        return $clone;
    }

    /**
     * @param iterable<string, callable|string> $map
     */
    public function withAddedMap(iterable $map): self
    {
        return $this->withGetters(GetterMap::fromIterable($map));
    }
}
