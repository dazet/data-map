Benchmarks
==========

### groups: object hydrator

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
ObjectHydratorBench | plainPhpHydrationOnce | 10000 | 1,057,672b | 0.000ms | 1.58x
ObjectHydratorBench | plainPhpConstructionOnce | 10000 | 1,057,680b | 0.000ms | 1.00x
ObjectHydratorBench | objectHydratorOnce | 10000 | 1,110,504b | 0.004ms | 18.66x
ObjectHydratorBench | objectConstructorOnce | 10000 | 12,837,384b | 0.003ms | 14.50x

### groups: object hydrator 100x

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
ObjectHydratorBench | plainPhpHydration100x | 10000 | 1,067,016b | 0.027ms | 1.87x
ObjectHydratorBench | plainPhpConstruction100x | 10000 | 1,066,928b | 0.015ms | 1.00x
ObjectHydratorBench | objectHydrator100x | 10000 | 1,124,952b | 0.163ms | 11.07x
ObjectHydratorBench | objectConstructor100x | 10000 | 12,854,224b | 0.073ms | 4.93x

### groups: pipeline transformation

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
PipelineTransformationBench | plainPhpRecursiveTransformation | 10000 | 1,131,040b | 0.007ms | 1.00x
PipelineTransformationBench | pipelineRecursiveTransformation | 10000 | 1,216,424b | 0.010ms | 1.40x

### groups: wrapper construct

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
WrappersBench | mixedWrappedConstruct | 10000 | 1,036,800b | 0.000ms | 1.00x
WrappersBench | recursiveWrappedConstruct | 10000 | 1,041,656b | 0.000ms | 1.02x
WrappersBench | pipelineWrappedConstruct | 10000 | 1,137,880b | 0.000ms | 3.94x

### groups: wrapping

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
SimpleDataWrappingBench | onlyMixedWrapping | 10000 | 1,187,472b | 0.003ms | 1.00x
SimpleDataWrappingBench | recursiveWrapping | 10000 | 1,193,200b | 0.003ms | 1.18x
SimpleDataWrappingBench | pipedRecursiveWrapping | 10000 | 1,225,672b | 0.004ms | 1.64x

### groups: wrapping 100x

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
SimpleDataWrappingBench | onlyMixedWrapping100x | 10000 | 1,187,472b | 0.148ms | 1.00x
SimpleDataWrappingBench | recursiveWrapping100x | 10000 | 1,195,504b | 0.195ms | 1.32x
SimpleDataWrappingBench | pipedMixedWrapping100x | 10000 | 1,217,912b | 0.229ms | 1.55x
SimpleDataWrappingBench | pipedRecursiveWrapping100x | 10000 | 1,232,216b | 0.276ms | 1.87x

