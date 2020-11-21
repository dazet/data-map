Benchmarks
==========

```shell
./bin/phpbench run benchmarks --report=basic --output=md --retry-threshold=5 --revs=1000
```

### groups: object hydrator

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
ObjectHydratorBench | plainPhpHydrationOnce | 1000 | 726,672b | 0.002ms | 1.31x
ObjectHydratorBench | plainPhpConstructionOnce | 1000 | 726,688b | 0.002ms | 1.00x
ObjectHydratorBench | objectHydratorOnce | 1000 | 769,936b | 0.027ms | 16.18x
ObjectHydratorBench | objectConstructorOnce | 1000 | 761,696b | 0.016ms | 9.68x

### groups: object hydrator 100x

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
ObjectHydratorBench | plainPhpHydration100x | 1000 | 726,672b | 0.173ms | 1.83x
ObjectHydratorBench | plainPhpConstruction100x | 1000 | 726,688b | 0.095ms | 1.00x
ObjectHydratorBench | objectHydrator100x | 1000 | 782,152b | 1.483ms | 15.68x
ObjectHydratorBench | objectConstructor100x | 1000 | 761,696b | 0.553ms | 5.85x

### groups: pipeline transformation

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
PipelineTransformationBench | plainPhp | 1000 | 747,968b | 0.052ms | 1.00x
PipelineTransformationBench | filteredGetter | 1000 | 889,128b | 0.105ms | 2.01x
PipelineTransformationBench | filtersPipeline | 1000 | 960,008b | 0.073ms | 1.39x

### groups: wrapper construct

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
WrappersBench | mixedWrappedConstruct | 1000 | 726,664b | 0.001ms | 1.00x
WrappersBench | recursiveWrappedConstruct | 1000 | 726,680b | 0.002ms | 1.02x
WrappersBench | pipelineWrappedConstruct | 1000 | 741,096b | 0.004ms | 2.38x

### groups: wrapping

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
SimpleDataWrappingBench | onlyMixedWrapping | 1000 | 795,920b | 0.020ms | 1.00x
SimpleDataWrappingBench | recursiveWrapping | 1000 | 814,920b | 0.026ms | 1.28x
SimpleDataWrappingBench | pipedRecursiveWrapping | 1000 | 856,776b | 0.038ms | 1.88x

### groups: wrapping 100x

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
SimpleDataWrappingBench | onlyMixedWrapping100x | 1000 | 802,056b | 1.045ms | 1.00x
SimpleDataWrappingBench | recursiveWrapping100x | 1000 | 821,032b | 1.545ms | 1.48x
SimpleDataWrappingBench | pipedMixedWrapping100x | 1000 | 845,984b | 2.099ms | 2.01x
SimpleDataWrappingBench | pipedRecursiveWrapping100x | 1000 | 862,376b | 2.541ms | 2.43x

