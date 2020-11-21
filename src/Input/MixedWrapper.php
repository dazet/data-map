<?php declare(strict_types=1);

namespace DataMap\Input;

use DataMap\Exception\FailedToWrapInput;
use function array_keys;
use function array_merge;
use function array_values;
use function class_implements;
use function get_class;
use function gettype;
use function implode;
use function sprintf;
use function strtolower;

final class MixedWrapper implements ExtensibleWrapper
{
    /** @var array<string, Wrapper> */
    private array $wrappers = [];

    public function __construct(Wrapper ...$wrappers)
    {
        foreach ($wrappers as $wrapper) {
            foreach ($wrapper->supportedTypes() as $type) {
                $this->wrappers[$type] = $wrapper;
            }
        }
    }

    public static function default(): self
    {
        static $self;

        return $self ?? $self = new self(new ArrayWrapper(), new ObjectWrapper(), new ScalarWrapper());
    }

    public static function extend(Wrapper $wrapper, Wrapper ...$extensions): Wrapper
    {
        if ($wrapper instanceof ExtensibleWrapper) {
            return $wrapper->withWrappers(...$extensions);
        }

        return new self($wrapper, ...$extensions);
    }

    public function supportedTypes(): array
    {
        return array_keys($this->wrappers);
    }

    /**
     * @param mixed $data
     * @throws FailedToWrapInput
     */
    public function wrap($data): Input
    {
        return $this->getWrapper($data)->wrap($data);
    }

    public function withWrappers(Wrapper ...$wrappers): ExtensibleWrapper
    {
        return new self(...array_values($this->wrappers), ...$wrappers);
    }

    /**
     * @param mixed $data
     * @return Wrapper
     */
    private function getWrapper($data): Wrapper
    {
        $types = $this->getTypes($data);

        foreach ($types as $type) {
            if (isset($this->wrappers[$type])) {
                return $this->wrappers[$type];
            }
        }

        throw new FailedToWrapInput(
            sprintf(
                'Type `%s` not supported, supported types are `%s`',
                $types[0],
                implode(', ', $this->supportedTypes())
            )
        );
    }

    /**
     * @param mixed $data
     * @return string[]
     */
    private function getTypes($data): array
    {
        $type = strtolower(gettype($data));

        if ($type === 'object') {
            // object wrapper priority:
            // 1. wrapper for given class
            // 2. wrapper for interface of that class
            // 3. generic object wrapper
            return array_merge([get_class($data)], class_implements($data), ['object']);
        }

        return [$type];
    }
}
