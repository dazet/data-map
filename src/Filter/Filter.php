<?php declare(strict_types=0);

namespace DataMap\Filter;

use Dazet\TypeUtil\NumberUtil;
use InvalidArgumentException;
use function array_filter;
use function array_key_exists;
use function array_replace;
use function array_search;
use function array_unshift;
use function array_values;
use function in_array;

/**
 * Filter is a function that takes only 1 argument and can be chained with other filters.
 * It encapsulates other function (optionally) with some predefined arguments and 1 variable argument.
 * Variable argument position can be declared by string `$$` in `$args` array.
 */
final class Filter
{
    /** Variable placeholder within `$args` */
    public const VARIABLE = '$$';
    /** Inherited value placeholder, used when copied `withArgs` */
    public const INHERITED = '_';

    /** @var callable */
    private $callback;

    /** @var mixed[] */
    private array $args;

    private int $varPosition;

    private bool $handleNull;

    /**
     * @param mixed[] $args
     */
    public function __construct(callable $callback, array $args = [], bool $handleNull = false)
    {
        $this->callback = $callback;
        $this->args = $this->prepareArgs($args);
        $this->varPosition = NumberUtil::toIntOrNull(array_search(self::VARIABLE, $this->args, true)) ?? 0;
        $this->handleNull = $handleNull;
    }

    /**
     * @param mixed[] $args
     */
    public static function wrap(callable $callback, array $args = []): self
    {
        if ($callback instanceof self) {
            return $callback->withArgs($args);
        }

        return new self($callback, $args);
    }

    /**
     * @param mixed[] $args
     */
    public static function nullable(callable $callback, array $args = []): self
    {
        if ($callback instanceof self) {
            return $callback->withArgs($args)->withNullHandling(true);
        }

        return new self($callback, $args, true);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function __invoke($value)
    {
        if ($value === null && !$this->handleNull) {
            return null;
        }

        $args = $this->args;
        $args[$this->varPosition] = $value;

        return ($this->callback)(...$args);
    }

    /**
     * @param mixed[] $args
     */
    public function withArgs(array $args): self
    {
        if ($args === []) {
            return $this;
        }

        return new self(
            $this->callback,
            // replace overridden arguments
            array_replace(
                $this->args,
                // do not override variable argument and inherited arguments
                array_filter(
                    $this->guessVariableArg($args),
                    static function ($arg): bool {
                        return $arg !== self::INHERITED && $arg !== self::VARIABLE;
                    }
                )
            ) ?? $this->args,
            $this->handleNull
        );
    }

    /**
     * @param mixed[] $args
     * @return mixed[]
     */
    private function prepareArgs(array $args): array
    {
        $args = array_values($args);
        if (!in_array(self::VARIABLE, $args, true)) {
            // variable argument at first position by default
            array_unshift($args, self::VARIABLE);
        }

        return $args;
    }

    /**
     * @param mixed[] $args
     * @return mixed[]
     */
    private function guessVariableArg(array $args): array
    {
        if (in_array(self::VARIABLE, $args, true)) {
            // filtered value argument position strictly defined
            return $args;
        }

        if ($this->varPosition === 0) {
            // filtered value at the beginning
            array_unshift($args, self::VARIABLE);

            return $args;
        }

        if (!array_key_exists($this->varPosition, $args)) {
            // insert variable argument onto predefined position
            $args[$this->varPosition] = self::VARIABLE;

            return $args;
        }

        throw new InvalidArgumentException('Variable argument position not defined and overwritten.');
    }

    private function withNullHandling(bool $handling): self
    {
        if ($this->handleNull === $handling) {
            return $this;
        }

        $clone = clone $this;
        $clone->handleNull = $handling;

        return $clone;
    }
}
