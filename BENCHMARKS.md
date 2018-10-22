Benchmarks
==========

### groups: object hydrator

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
ObjectHydratorBench | plainPhpHydrationOnce | 1 | 971,272b | 0.005ms | 1.67x
ObjectHydratorBench | plainPhpConstructionOnce | 1 | 971,280b | 0.003ms | 1.00x
ObjectHydratorBench | objectHydratorOnce | 1 | 1,022,408b | 0.222ms | 74.00x
ObjectHydratorBench | objectConstructorOnce | 1 | 1,014,336b | 0.204ms | 68.00x

### groups: object hydrator 100x

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
ObjectHydratorBench | plainPhpHydration100x | 1 | 980,616b | 0.037ms | 1.61x
ObjectHydratorBench | plainPhpConstruction100x | 1 | 980,528b | 0.023ms | 1.00x
ObjectHydratorBench | objectHydrator100x | 1 | 1,038,352b | 0.406ms | 17.65x
ObjectHydratorBench | objectConstructor100x | 1 | 1,014,336b | 0.410ms | 17.83x

### groups: wrapping

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
SimpleDataWrappingBench | onlyMixedWrapping | 1 | 1,045,208b | 0.640ms | 1.00x
SimpleDataWrappingBench | recursiveWrapping | 1 | 1,058,760b | 0.861ms | 1.35x
SimpleDataWrappingBench | pipedRecursiveWrapping | 1 | 1,162,424b | 2.121ms | 3.31x

### groups: wrapping 100x

benchmark | subject | revs | mem_peak | time_rev | diff
 --- | --- | --- | --- | --- | --- 
SimpleDataWrappingBench | onlyMixedWrapping100x | 1 | 1,052,064b | 0.788ms | 1.00x
SimpleDataWrappingBench | recursiveWrapping100x | 1 | 1,065,344b | 0.943ms | 1.20x
SimpleDataWrappingBench | pipedRecursiveWrapping100x | 1 | 1,169,968b | 2.780ms | 3.53x

