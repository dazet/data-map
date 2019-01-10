<?php declare(strict_types=1);

use DataMap\Input\MixedWrapper;
use DataMap\Input\FilteredWrapper;
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
final class SimpleDataWrappingBench
{
    private const MAP = [
        'mapped_field_1' => 'field_1',
        'mapped_field_2' => 'field_2',
        'mapped_field_3' => 'field_3',
    ];

    private $data = [
        'field_1' => 'field 1 content',
        'field_2' => 'field 2 content',
        'field_3' => 'field 3 content',
    ];

    /** @var array */
    private $data100x;

    public function __construct()
    {
        $this->data100x = array_map(
            function (int $i): array {
                return [
                    'field_1' => "field {$i}.1 content",
                    'field_2' => "field {$i}.2 content",
                    'field_3' => "field {$i}.3 content"
                ];
            },
            range(1, 100)
        );
    }

    /**
     * @Subject
     * @Groups({"wrapping"})
     */
    public function onlyMixedWrapping(): void
    {
        $mapper = new Mapper(self::MAP, null, MixedWrapper::default());
        $mapper->map($this->data);
    }

    /**
     * @Subject
     * @Groups({"wrapping"})
     */
    public function recursiveWrapping(): void
    {
        $mapper = new Mapper(self::MAP, null, RecursiveWrapper::default());
        $mapper->map($this->data);
    }

    /**
     * @Subject
     * @Groups({"wrapping"})
     */
    public function pipedRecursiveWrapping(): void
    {
        $mapper = new Mapper(self::MAP, null, FilteredWrapper::default());
        $mapper->map($this->data);
    }

    /**
     * @Subject
     * @Groups({"wrapping 100x"})
     */
    public function onlyMixedWrapping100x(): void
    {
        $mapper = new Mapper(self::MAP, null, MixedWrapper::default());
        $result = [];

        foreach ($this->data100x as $i => $data) {
            $result[$i] = $mapper->map($data);
        }
    }

    /**
     * @Subject
     * @Groups({"wrapping 100x"})
     */
    public function recursiveWrapping100x(): void
    {
        $mapper = new Mapper(self::MAP, null, RecursiveWrapper::default());
        $result = [];

        foreach ($this->data100x as $i => $data) {
            $result[$i] = $mapper->map($data);
        }
    }

    /**
     * @Subject
     * @Groups({"wrapping 100x"})
     */
    public function pipedMixedWrapping100x(): void
    {
        $mapper = new Mapper(self::MAP, null, new FilteredWrapper(MixedWrapper::default()));
        $result = [];

        foreach ($this->data100x as $i => $data) {
            $result[$i] = $mapper->map($data);
        }
    }

    /**
     * @Subject
     * @Groups({"wrapping 100x"})
     */
    public function pipedRecursiveWrapping100x(): void
    {
        $mapper = new Mapper(self::MAP, null, FilteredWrapper::default());
        $result = [];

        foreach ($this->data100x as $i => $data) {
            $result[$i] = $mapper->map($data);
        }
    }
}
