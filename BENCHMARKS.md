Benchmarks
==========

### suite: 133f01e0affbe869a521a078e2a00e4e60abbb46, date: 2018-10-22, stime: 08:02:26

benchmark | subject | groups | params | revs | iter | mem_peak | time_rev | comp_z_value | comp_deviation
 --- | --- | --- | --- | --- | --- | --- | --- | --- | --- 
ObjectHydratorBench | plainPhpHydrationOnce | object hydrator | [] | 1 | 0 | 971,192b | 5.000μs | 0.00σ | 0.00%
ObjectHydratorBench | plainPhpConstructionOnce | object hydrator | [] | 1 | 0 | 971,200b | 2.000μs | 0.00σ | 0.00%
ObjectHydratorBench | objectHydratorOnce | object hydrator | [] | 1 | 0 | 1,022,328b | 245.000μs | 0.00σ | 0.00%
ObjectHydratorBench | objectConstructorOnce | object hydrator | [] | 1 | 0 | 1,014,256b | 176.000μs | 0.00σ | 0.00%
ObjectHydratorBench | plainPhpHydration100x | object hydrator 100x | [] | 1 | 0 | 980,536b | 39.000μs | 0.00σ | 0.00%
ObjectHydratorBench | plainPhpConstruction100x | object hydrator 100x | [] | 1 | 0 | 980,448b | 25.000μs | 0.00σ | 0.00%
ObjectHydratorBench | objectHydrator100x | object hydrator 100x | [] | 1 | 0 | 1,038,272b | 554.000μs | 0.00σ | 0.00%
ObjectHydratorBench | objectConstructor100x | object hydrator 100x | [] | 1 | 0 | 1,014,256b | 346.000μs | 0.00σ | 0.00%
SimpleDataWrappingBench | onlyMixedWrapping | wrapping | [] | 1 | 0 | 1,045,048b | 641.000μs | 0.00σ | 0.00%
SimpleDataWrappingBench | recursiveWrapping | wrapping | [] | 1 | 0 | 1,058,600b | 759.000μs | 0.00σ | 0.00%
SimpleDataWrappingBench | pipedRecursiveWrapping | wrapping | [] | 1 | 0 | 1,058,600b | 752.000μs | 0.00σ | 0.00%
SimpleDataWrappingBench | onlyMixedWrapping100x | wrapping 100x | [] | 1 | 0 | 1,051,904b | 784.000μs | 0.00σ | 0.00%
SimpleDataWrappingBench | recursiveWrapping100x | wrapping 100x | [] | 1 | 0 | 1,065,184b | 1,062.000μs | 0.00σ | 0.00%
SimpleDataWrappingBench | pipedRecursiveWrapping100x | wrapping 100x | [] | 1 | 0 | 1,065,192b | 941.000μs | 0.00σ | 0.00%

