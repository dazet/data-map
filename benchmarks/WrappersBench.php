<?php declare(strict_types=1);

use DataMap\Input\MixedWrapper;
use DataMap\Input\FilteredWrapper;
use DataMap\Input\RecursiveWrapper;
use PhpBench\Benchmark\Metadata\Annotations\Groups;
use PhpBench\Benchmark\Metadata\Annotations\OutputTimeUnit;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Subject;

/**
 * @OutputTimeUnit("milliseconds", precision=3)
 * @Revs(1000)
 */
final class WrappersBench
{
    /**
     * @Subject
     * @Groups({"wrapper construct"})
     */
    public function mixedWrappedConstruct(): void
    {
        MixedWrapper::default();
    }

    /**
     * @Subject
     * @Groups({"wrapper construct"})
     */
    public function recursiveWrappedConstruct(): void
    {
        RecursiveWrapper::default();
    }

    /**
     * @Subject
     * @Groups({"wrapper construct"})
     */
    public function pipelineWrappedConstruct(): void
    {
        FilteredWrapper::recursive();
    }
}
