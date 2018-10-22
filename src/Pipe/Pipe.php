<?php

namespace DataMap\Pipe;

/**
 * Pipe is a callback that takes only 1 argument.
 * It encapsulates other function with some predefined arguments and 1 variable argument.
 * Variable argument position can be declared by string `$$` in `$args`.
 */
final class Pipe
{
    /** Variable placeholder within `$args` */
    public const VARIABLE = '$$';

    /** Inherited value placeholder when copied `withArgs` */
    public const INHERITED = '_';

    /** @var callable */
    private $callback;

    /** @var array */
    private $args;

    /** @var int */
    private $varPosition;

    /** @var bool */
    private $allowNull;

    public function __construct(callable $callback, array $args = [], bool $allowNull = false)
    {
        $this->callback = $callback;
        $this->args = $this->prepareArgs($args);
        $this->varPosition = \array_search(self::VARIABLE, $this->args, true);
        $this->allowNull = $allowNull;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function __invoke($value)
    {
        if ($value === null && !$this->allowNull) {
            return null;
        }

        $args = $this->args;
        $args[$this->varPosition] = $value;

        return ($this->callback)(...$args);
    }

    public function withArgs(array $args): self
    {
        return new self(
            $this->callback,
            \array_replace(
                $this->args,
                \array_filter(
                    $this->guessVariableArg($args),
                    function ($arg): bool {
                        return $arg !== self::INHERITED && $arg !== self::VARIABLE;
                    }
                )
            ),
            $this->allowNull
        );
    }

    private function prepareArgs(array $args): array
    {
        $args = \array_values($args);
        if (!\in_array(self::VARIABLE, $args, true)) {
            \array_unshift($args, self::VARIABLE);
        }

        return $args;
    }

    private function guessVariableArg(array $args): array
    {
        if (\in_array(self::VARIABLE, $args, true)) {
            return $args;
        }

        if ($this->varPosition === 0) {
            // pipe argument first
            \array_unshift($args, self::VARIABLE);

            return $args;
        }

        if (!\array_key_exists($this->varPosition, $args)) {
            $args[$this->varPosition] = self::VARIABLE;

            return $args;
        }

        throw new \InvalidArgumentException(
            \sprintf(
                'Unable resolve variable argument. Pipe args: %s, other args: %s',
                \json_encode($this->args),
                \json_encode($args)
            )
        );
    }
}
