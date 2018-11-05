Benchmarks
==========

### groups: object hydrator

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
ObjectHydratorBench | plainPhpHydrationOnce | 1000 | 1,061,712b | 0.002ms | 1.54x
ObjectHydratorBench | plainPhpConstructionOnce | 1000 | 1,061,720b | 0.001ms | 1.00x
ObjectHydratorBench | objectHydratorOnce | 1000 | 1,116,464b | 0.026ms | 17.95x
ObjectHydratorBench | objectConstructorOnce | 1000 | 2,382,720b | 0.014ms | 10.04x

### groups: object hydrator 100x

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
ObjectHydratorBench | plainPhpHydration100x | 1000 | 1,071,056b | 0.164ms | 1.92x
ObjectHydratorBench | plainPhpConstruction100x | 1000 | 1,070,968b | 0.085ms | 1.00x
ObjectHydratorBench | objectHydrator100x | 1000 | 1,132,824b | 1.497ms | 17.52x
ObjectHydratorBench | objectConstructor100x | 1000 | 2,415,944b | 0.398ms | 4.66x

### groups: pipeline transformation

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
PipelineTransformationBench | plainPhpRecursiveTransformation | 1000 | 1,088,832b | 0.058ms | 1.00x
PipelineTransformationBench | pipelineRecursiveTransformation | 1000 | 1,183,320b | 0.078ms | 1.35x

### groups: wrapper construct

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
WrappersBench | mixedWrappedConstruct | 1000 | 1,039,088b | 0.001ms | 1.00x
WrappersBench | recursiveWrappedConstruct | 1000 | 1,043,848b | 0.002ms | 1.15x
WrappersBench | pipelineWrappedConstruct | 1000 | 1,099,040b | 0.003ms | 2.52x

### groups: wrapping

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
SimpleDataWrappingBench | onlyMixedWrapping | 1000 | 1,145,592b | 0.019ms | 1.00x
SimpleDataWrappingBench | recursiveWrapping | 1000 | 1,162,008b | 0.026ms | 1.33x
SimpleDataWrappingBench | pipedRecursiveWrapping | 1000 | 1,219,872b | 0.037ms | 1.92x

### groups: wrapping 100x

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
SimpleDataWrappingBench | onlyMixedWrapping100x | 1000 | 1,152,416b | 1.049ms | 1.00x
SimpleDataWrappingBench | recursiveWrapping100x | 1000 | 1,169,024b | 1.666ms | 1.59x
SimpleDataWrappingBench | pipedMixedWrapping100x | 1000 | 1,210,296b | 1.796ms | 1.71x
SimpleDataWrappingBench | pipedRecursiveWrapping100x | 1000 | 1,226,848b | 2.342ms | 2.23x

