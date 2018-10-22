<?php

namespace DataMap\Pipe;

use DataMap\Exception\FailedToParseGetter;

/**
 * Parse getter configured as a pipeline of functions (pipes).
 * Example: `input.key | string | trim | strip_tags`
 */
final class PipelineParser
{
    /** @var callable[][] */
    public const DEFAULT_PIPES = [
        // key => [Pipe callback, [Pipe constructor args]]
        // types
        'string' => ['DataMap\Common\StringUtil::toStringOrNull'],
        'int' => ['DataMap\Common\NumberUtil::toIntOrNull'],
        'integer' => ['DataMap\Common\NumberUtil::toIntOrNull'],
        'float' => ['DataMap\Common\NumberUtil::toFloatOrNull'],
        'bool' => ['DataMap\Common\BooleanUtil::toBoolOrNull'],
        'boolean' => ['DataMap\Common\BooleanUtil::toBoolOrNull'],
        'array' => ['DataMap\Common\ArrayUtil::toArrayOrNull'],
        // strings
        'explode' => ['explode', [',', '$$']],
        'implode' => ['implode', [',', '$$']],
        'upper' => ['mb_strtoupper'],
        'lower' => ['mb_strtolower'],
        'trim' => ['trim'],
        'rtrim' => ['rtrim'],
        'ltrim' => ['ltrim'],
        'format' => ['sprintf', ['%s', '$$']],
        'replace' => ['str_replace', ['', '', '$$']],
        'strip_tags' => ['strip_tags'],
        // numbers
        'number_format' => ['number_format', ['$$', 2, '.', '']],
        'round' => ['round', ['$$', 0]],
        'floor' => ['floor', ['$$']],
        'ceil' => ['ceil', ['$$']],
        // dates
        'datetime' => ['DataMap\Common\DateUtil::toDatetimeOrNull'],
        'date_format' => ['DataMap\Common\DateUtil::toDateFormatOrNull', ['$$', 'Y-m-d H:i:s']],
        'date_modify' => ['DataMap\Common\DateUtil::dateModifyOrNull', ['$$', '+0 seconds']],
        'timestamp' => ['DataMap\Common\DateUtil::toTimestampOrNull'],
        // json
        'json_encode' => ['DataMap\Common\JsonUtil::toJsonOrNull'],
        'json_decode' => ['DataMap\Common\JsonUtil::toArrayOrNull'],
        // misc
        'count' => ['DataMap\Common\ArrayUtil::countOrNull'],
        'if_null' => ['DataMap\Common\VariableUtil::ifNull', ['$$', null], true],
        'if_empty' => ['DataMap\Common\VariableUtil::ifEmpty', ['$$', null], true],
    ];
    public const ARG_REPLACE = [':null' => null, ':false' => false, ':true' => true, ':[]' => []];
    public const STR_REPLACE = ['\\|' => '|'];

    /** @var Pipe[] array<string, Pipe> [pipe_function_name => Pipe, ...] */
    private $pipesMap;

    /**
     * Allow any PHP function (or other callable passed as string) when pipe function name is not defined.
     * In safe mode you will not be able to use `key | strval | trim` unless you strictly define these pipe functions.
     * @var bool
     */
    private $allowFunction;

    /**
     * @param Pipe[] $pipesMap array<string, Pipe> [pipe_function_name => Pipe, ...]
     */
    public function __construct(array $pipesMap, bool $allowFunction = true)
    {
        $this->pipesMap = [];
        $this->allowFunction = $allowFunction;
        $this->addPipes($pipesMap);
    }

    public static function default(): self
    {
        static $default;

        return $default ?? $default = new self(
            \array_map(
                function (array $pipeArgs): Pipe {
                    return new Pipe(...$pipeArgs);
                },
                self::DEFAULT_PIPES
            )
        );
    }

    public static function safeDefault(): self
    {
        $self = self::default();
        $self->allowFunction = false;

        return $self;
    }

    public function parse(string $getter): Pipeline
    {
        $pipeline = \array_map('trim', \preg_split('/[^\\\\]\|/', $getter));

        // first element should be input key
        $key = \array_shift($pipeline);

        // rest should be list of pipes definitions
        $pipes = \array_map(
            function (string $pipeDef): Pipe {
                $pipeArgs = \str_getcsv(\trim($pipeDef), ' ');
                $pipeKey = \array_shift($pipeArgs);

                return $this->get($pipeKey, $this->parseArgs($pipeArgs));
            },
            $pipeline
        );

        return new Pipeline($key, ...$pipes);
    }

    /**
     * @param Pipe[] $pipes
     */
    public function withPipes(array $pipes): self
    {
        $copy = clone $this;
        $copy->addPipes($pipes);

        return $copy;
    }

    public function withPipe(string $key, Pipe $pipe): self
    {
        $copy = clone $this;
        $copy->addPipes([$key => $pipe]);

        return $copy;
    }

    /**
     * @param Pipe[] $map array<string, Pipe>
     */
    private function addPipes(array $map): void
    {
        foreach ($map as $key => $pipe) {
            if (!$pipe instanceof Pipe) {
                throw new \InvalidArgumentException('PipelineParser can contain only Pipe instances');
            }

            $key = \trim((string)$key);

            if ($key === '') {
                throw new \InvalidArgumentException('Pipe key should not be empty');
            }

            $this->pipesMap[$key] = $pipe;
        }
    }

    private function get(string $key, array $args = []): Pipe
    {
        if (isset($this->pipesMap[$key])) {
            return $this->pipesMap[$key]->withArgs($args);
        }

        if ($this->allowFunction && \is_callable($key)) {
            return new Pipe($key, $args);
        }

        throw new FailedToParseGetter(sprintf('Cannot resolve pipe function for %s', $key));
    }

    private function parseArgs(array $args): array
    {
        return \array_map(
            function (string $arg) {
                return self::ARG_REPLACE[$arg] ?? \strtr($arg, self::STR_REPLACE);
            },
            $args
        );
    }
}
