Benchmarks
==========

### groups: object hydrator

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
ObjectHydratorBench | plainPhpHydrationOnce | 1000 | 975,440b | 0.000ms | 1.00x
ObjectHydratorBench | plainPhpConstructionOnce | 1000 | 975,448b | 0.000ms | 1.23x
ObjectHydratorBench | objectHydratorOnce | 1000 | 1,030,376b | 0.004ms | 13.93x
ObjectHydratorBench | objectConstructorOnce | 1000 | 2,141,088b | 0.003ms | 10.21x

### groups: object hydrator 100x

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
ObjectHydratorBench | plainPhpHydration100x | 1000 | 984,784b | 0.026ms | 1.72x
ObjectHydratorBench | plainPhpConstruction100x | 1000 | 984,696b | 0.015ms | 1.00x
ObjectHydratorBench | objectHydrator100x | 1000 | 1,042,496b | 0.154ms | 10.13x
ObjectHydratorBench | objectConstructor100x | 1000 | 2,174,376b | 0.072ms | 4.71x

### groups: pipeline transformation

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
PipelineTransformationBench | plainPhp | 1000 | 1,069,056b | 0.008ms | 1.00x
PipelineTransformationBench | filteredGetter | 1000 | 1,132,944b | 0.016ms | 2.15x
PipelineTransformationBench | filtersPipeline | 1000 | 1,259,920b | 0.011ms | 1.45x

### groups: wrapper construct

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
WrappersBench | mixedWrappedConstruct | 1000 | 954,688b | 0.000ms | 1.00x
WrappersBench | recursiveWrappedConstruct | 1000 | 959,600b | 0.000ms | 1.11x
WrappersBench | pipelineWrappedConstruct | 1000 | 1,064,168b | 0.001ms | 2.36x

### groups: wrapping

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
SimpleDataWrappingBench | onlyMixedWrapping | 1000 | 1,117,368b | 0.003ms | 1.00x
SimpleDataWrappingBench | recursiveWrapping | 1000 | 1,132,840b | 0.004ms | 1.19x
SimpleDataWrappingBench | pipedRecursiveWrapping | 1000 | 1,168,808b | 0.005ms | 1.59x

### groups: wrapping 100x

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
SimpleDataWrappingBench | onlyMixedWrapping100x | 1000 | 1,123,232b | 0.129ms | 1.00x
SimpleDataWrappingBench | recursiveWrapping100x | 1000 | 1,137,640b | 0.176ms | 1.36x
SimpleDataWrappingBench | pipedMixedWrapping100x | 1000 | 1,160,072b | 0.205ms | 1.58x
SimpleDataWrappingBench | pipedRecursiveWrapping100x | 1000 | 1,174,456b | 0.256ms | 1.98x

