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

    /** @var Pipeline[] */
    private $parsed = [];

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

        return $default ?? $default = new self([]);
    }

    public static function safeDefault(): self
    {
        static $default;

        return $default ?? $default = new self([], false);
    }

    public function parse(string $getter): Pipeline
    {
        if (isset($this->parsed[$getter])) {
            return $this->parsed[$getter];
        }

        if (\strpos($getter, '|') === false) {
            return $this->parsed[$getter] = new Pipeline($getter);
        }

        // split by `|` except escaped `\|`
        $pipeline = \preg_split('/[^\\\\]\|/', $getter);

        if ($pipeline === false) {
            throw new FailedToParseGetter('Failed to split transformation pipes');
        }

        $pipeline = \array_map('\trim', $pipeline);

        // first element should be input key
        $key = (string)\array_shift($pipeline);

        if ($key === '') {
            throw new FailedToParseGetter('Input key is empty');
        }

        // rest should be list of pipes definitions

        return $this->parsed[$getter] = new Pipeline($key, ...$this->parsePipes($pipeline));
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
        return $this->withPipes([$key => $pipe]);
    }

    /**
     * @param Pipe[] $map array<string, Pipe>
     */
    private function addPipes(array $map): void
    {
        foreach ($map as $key => $pipe) {
            if (!$pipe instanceof Pipe) {
                throw new FailedToParseGetter('PipelineParser can contain only Pipe instances');
            }

            $key = \trim((string)$key);

            if ($key === '') {
                throw new FailedToParseGetter('Pipe key should not be empty');
            }

            $this->pipesMap[$key] = $pipe;
        }
    }

    private function get(string $key, array $args = []): Pipe
    {
        if (isset($this->pipesMap[$key])) {
            return $this->pipesMap[$key]->withArgs($args);
        }

        if (isset(self::DEFAULT_PIPES[$key])) {
            $this->pipesMap[$key] = new Pipe(...self::DEFAULT_PIPES[$key]);

            return $this->pipesMap[$key]->withArgs($args);
        }

        if ($this->allowFunction && \is_callable($key)) {
            return new Pipe($key, $args);
        }

        throw new FailedToParseGetter("Cannot resolve pipe function for {$key}");
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

    /**
     * @param string[] $pipeline
     * @return Pipe[]
     * @throws FailedToParseGetter
     */
    private function parsePipes(array $pipeline): array
    {
        return \array_map(
            function (string $pipeDef) {
                $pipeArgs = \str_getcsv($pipeDef, ' ');
                $pipeKey = \array_shift($pipeArgs);

                return $this->get($pipeKey, $this->parseArgs($pipeArgs));
            },
            $pipeline
        );
    }
}
