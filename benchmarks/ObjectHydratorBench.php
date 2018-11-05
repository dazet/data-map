<?php

use DataMap\Output\ObjectConstructor;
use DataMap\Output\ObjectHydrator;
use PhpBench\Benchmark\Metadata\Annotations\Groups;
use PhpBench\Benchmark\Metadata\Annotations\OutputTimeUnit;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Subject;

/**
 * @OutputTimeUnit("milliseconds", precision=3)
 * @Revs(1000)
 */
final class ObjectHydratorBench
{
    private $dto;

    private $dataOnce;

    private $data100x;

    public function __construct()
    {
        $this->dto = new ExampleDto();

        $this->dataOnce = [
            'public' => 'public value',
            'settable' => 'setter value',
            'clonable' => 'cloned value',
        ];

        $this->data100x = array_map(
            function (int $i): array {
                return [
                    'public' => 'public ' . $i,
                    'settable' => 'setter '. $i,
                    'clonable' => 'cloned '. $i,
                ];
            },
            range(1, 100)
        );
    }

    /**
     * @Subject
     * @Groups({"object hydrator"})
     */
    public function plainPhpHydrationOnce(): void
    {
        $dto = clone $this->dto;
        $dto->public = $this->dataOnce['public'];
        $dto->setSettable($this->dataOnce['settable']);
        $dto->withClonable($this->dataOnce['clonable']);
    }

    /**
     * @Subject
     * @Groups({"object hydrator"})
     */
    public function plainPhpConstructionOnce(): void
    {
        new ExampleDto($this->dataOnce['public'], $this->dataOnce['settable'], $this->dataOnce['clonable']);
    }

    /**
     * @Subject
     * @Groups({"object hydrator"})
     */
    public function objectHydratorOnce(): void
    {
        (new ObjectHydrator(clone $this->dto))->format($this->dataOnce);
    }

    /**
     * @Subject
     * @Groups({"object hydrator"})
     */
    public function objectConstructorOnce(): void
    {
        (new ObjectConstructor(ExampleDto::class))->format($this->dataOnce);
    }

    /**
     * @Subject
     * @Groups({"object hydrator 100x"})
     */
    public function plainPhpHydration100x(): void
    {
        $result = [];

        foreach ($this->data100x as $i => $data) {
            $result[$i] = clone $this->dto;
            $result[$i]->public = $data['public'];
            $result[$i]->setSettable($data['settable']);
            $result[$i]->withClonable($data['clonable']);
        }
    }

    /**
     * @Subject
     * @Groups({"object hydrator 100x"})
     */
    public function plainPhpConstruction100x(): void
    {
        $result = [];

        foreach ($this->data100x as $i => $data) {
            $result[$i] = new ExampleDto($data['public'], $data['settable'], $data['clonable']);
        }
    }

    /**
     * @Subject
     * @Groups({"object hydrator 100x"})
     */
    public function objectHydrator100x(): void
    {
        $hydrator = new ObjectHydrator(clone $this->dto);
        array_map([$hydrator, 'format'], $this->data100x);
    }

    /**
     * @Subject
     * @Groups({"object hydrator 100x"})
     */
    public function objectConstructor100x(): void
    {
        $hydrator = new ObjectConstructor(ExampleDto::class);
        array_map([$hydrator, 'format'], $this->data100x);
    }
}

class ExampleDto
{
    public $public;

    private $settable;

    private $clonable;

    public function __construct($public = null, $settable = null, $clonable = null)
    {
        $this->public = $public;
        $this->settable = $settable;
        $this->clonable = $clonable;
    }

    public function setSettable($value): void
    {
        $this->settable = $value;
    }

    public function withClonable($value): self
    {
        $copy = clone $this;
        $copy->clonable = $value;

        return $copy;
    }
}
