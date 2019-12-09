<?php declare(strict_types=1);

namespace DataMap\Getter;

use DataMap\Common\BooleanUtil;
use DataMap\Input\Input;

final class GetBoolean implements Getter
{
    public const TRUTH = BooleanUtil::TRUTHS;
    public const FALSEHOOD = BooleanUtil::FALLACY;

    /** @var string */
    private $key;

    /** @var bool|null */
    private $default;

    public function __construct(string $key, ?bool $default = null)
    {
        $this->key = $key;
        $this->default = $default;
    }

    public function __invoke(Input $input): ?bool
    {
        $value = $input->get($this->key);

        return BooleanUtil::toBoolOrNull($value) ?? $this->default;
    }
}
