<?php declare(strict_types=1);

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
    public function plainPhpRecursiveTransformation(): void
    {
        $mapper = new Mapper(
            [
                'string' => function (Input $input): string {
                    return \strtoupper(\trim((string)$input->get('string.value')));
                },
                'integer' => function (Input $input): int {
                    return (int)$input->get('integer.value');
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
    public function pipelineRecursiveTransformation(): void
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
