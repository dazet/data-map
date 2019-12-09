<?php declare(strict_types=1);

use DataMap\Getter\GetFiltered;
use DataMap\Input\FilteredWrapper;
use DataMap\Input\Input;
use DataMap\Input\RecursiveWrapper;
use DataMap\Mapper;
use PhpBench\Benchmark\Metadata\Annotations\Groups;
use PhpBench\Benchmark\Metadata\Annotations\OutputTimeUnit;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Subject;

/**
 * @OutputTimeUnit("milliseconds", precision=3)
 * @Revs(1000)
 */
final class PipelineTransformationBench
{
    /**
     * @Subject
     * @Groups({"pipeline transformation"})
     */
    public function plainPhp(): void
    {
        $mapper = new Mapper(
            [
                'string' => function (Input $input): ?string {
                    $value = $input->get('string.value');

                    if (is_string($value)) {
                        return strtoupper(trim($value));
                    }

                    return null;
                },
                'integer' => function (Input $input): ?int {
                    $value = $input->get('integer.value');

                    return is_int($value) || (is_string($value) && ctype_digit($value)) ? (int)$value : null;
                }
            ],
            null,
            RecursiveWrapper::default()
        );

        $mapper->map(['string' => ['value' => '  asdfghjkl  '], 'integer' => ['value' => '123456']]);
    }

    /**
     * @Subject
     * @Groups({"pipeline transformation"})
     */
    public function filteredGetter(): void
    {
        $mapper = new Mapper(
            [
                'string' => GetFiltered::from('string.value')->string()->trim()->upper(),
                'integer' => GetFiltered::from('integer.value')->int(),
            ],
            null,
            RecursiveWrapper::default()
        );

        $mapper->map(['string' => ['value' => '  asdfghjkl  '], 'integer' => ['value' => '123456']]);
    }

    /**
     * @Subject
     * @Groups({"pipeline transformation"})
     */
    public function filtersPipeline(): void
    {
        $mapper = new Mapper(
            [
                'string' => 'string.value | string | trim | upper',
                'integer' => 'integer.value | int'
            ],
            null,
            FilteredWrapper::default()
        );

        $mapper->map(['string' => ['value' => '  asdfghjkl  '], 'integer' => ['value' => '123456']]);
    }
}
