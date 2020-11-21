<?php declare(strict_types=1);

namespace DataMap\Getter;

use DataMap\Common\VariableUtil;
use DataMap\Exception\FailedToParseGetter;
use DataMap\Filter\Filter;
use DataMap\Filter\Filters;
use DataMap\Input\Input;
use Dazet\TypeUtil\ArrayUtil;
use Dazet\TypeUtil\BooleanUtil;
use Dazet\TypeUtil\DateUtil;
use Dazet\TypeUtil\NumberUtil;
use Dazet\TypeUtil\StringUtil;
use function explode;
use function implode;
use function number_format;
use function round;
use function sprintf;
use function str_replace;

/**
 * Get value from input and pass it through sequence of functions
 */
final class GetFiltered implements Getter
{
    private string $key;

    private Filters $filters;

    public function __construct(string $key, callable ...$filters)
    {
        $this->key = $key;
        $this->filters = Filters::fromCallable(...$filters);
    }

    public static function from(string $key, callable ...$filters): self
    {
        return new self($key, ...$filters);
    }

    /**
     * @return mixed
     */
    public function __invoke(Input $input)
    {
        $value = $input->get($this->key);

        return $this->filters->transform($value);
    }

    public function with(callable $filters): self
    {
        $clone = clone $this;
        $clone->filters = $this->filters->with($filters);

        return $clone;
    }

    public function withNullable(callable $filters): self
    {
        $clone = clone $this;
        $clone->filters = $this->filters->withNullable($filters);

        return $clone;
    }

    public function string(): self
    {
        return $this->with(StringUtil::toStringOrNull);
    }

    public function int(): self
    {
        return $this->with(NumberUtil::toIntOrNull);
    }

    public function float(): self
    {
        return $this->with(NumberUtil::toFloatOrNull);
    }

    public function bool(): self
    {
        return $this->with(BooleanUtil::toBoolOrNull);
    }

    public function array(): self
    {
        return $this->with(ArrayUtil::toArrayOrNull);
    }

    public function explode(string $delimiter): self
    {
        if ($delimiter === '') {
            throw new FailedToParseGetter('Empty delimiter in explode filter');
        }

        return $this->with(static function (string $string) use ($delimiter): array {
            return explode($delimiter, $string) ?: [];
        });
    }

    public function implode(string $glue): self
    {
        return $this->with(static function (array $pieces) use ($glue): string {
            return implode($glue, $pieces);
        });
    }

    public function upper(): self
    {
        return $this->with('\mb_strtoupper');
    }

    public function lower(): self
    {
        return $this->with('\mb_strtolower');
    }

    public function trim(): self
    {
        return $this->with('\trim');
    }

    public function format(string $template): self
    {
        return $this->with(static function (string $string) use ($template): string {
            return sprintf($template, $string);
        });
    }

    public function replace(string $search, string $replace): self
    {
        return $this->with(static function (string $subject) use ($search, $replace): string {
            return str_replace($search, $replace, $subject);
        });
    }

    public function stripTags(): self
    {
        return $this->with('\strip_tags');
    }

    public function numberFormat(int $decimals = 0, string $decimalPoint = '.', string $thousandsSeparator = ','): self
    {
        return $this->with(static function ($number) use ($decimals, $decimalPoint, $thousandsSeparator): string {
            return number_format((float)$number, $decimals, $decimalPoint, $thousandsSeparator);
        });
    }

    public function round(int $precision = 0): self
    {
        return $this->with(static function ($number) use ($precision): float {
            return round((float)$number, $precision);
        });
    }

    public function floor(): self
    {
        return $this->with('\floor');
    }

    public function ceil(): self
    {
        return $this->with('\ceil');
    }

    public function date(): self
    {
        return $this->with(DateUtil::toDatetimeOrNull);
    }

    public function dateFormat(string $format): self
    {
        return $this->with(static function ($value) use ($format): ?string {
            return DateUtil::toDateFormatOrNull($value, $format);
        });
    }

    public function count(): self
    {
        return $this->with(ArrayUtil::countOrNull);
    }

    /**
     * @param mixed $default
     */
    public function ifNull($default): self
    {
        return $this->with(Filter::nullable(VariableUtil::ifNull, ['$$', $default]));
    }

    /**
     * @param mixed $default
     */
    public function ifEmpty($default): self
    {
        return $this->with(Filter::nullable(VariableUtil::ifEmpty, ['$$', $default]));
    }
}
