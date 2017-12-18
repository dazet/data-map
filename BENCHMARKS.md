Benchmarks
==========

### groups: object hydrator

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
ObjectHydratorBench | plainPhpHydrationOnce | 100 | 854,968b | 0.390μs | 1.56x
ObjectHydratorBench | plainPhpConstructionOnce | 100 | 854,976b | 0.250μs | 1.00x
ObjectHydratorBench | objectHydratorOnce | 100 | 971,152b | 6.330μs | 25.32x
ObjectHydratorBench | objectConstructorOnce | 100 | 1,056,552b | 5.210μs | 20.84x

### groups: object hydrator 100x

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
ObjectHydratorBench | plainPhpHydration100x | 100 | 864,312b | 35.690μs | 1.94x
ObjectHydratorBench | plainPhpConstruction100x | 100 | 864,224b | 18.360μs | 1.00x
ObjectHydratorBench | objectHydrator100x | 100 | 987,520b | 190.090μs | 10.35x
ObjectHydratorBench | objectConstructor100x | 100 | 1,073,392b | 89.470μs | 4.87x

