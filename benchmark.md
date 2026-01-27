### Benchmark Results

#### Branch: `main` (Optimized)
| Benchmark | Subject | Mo (Mean) | Mem Peak |
|-----------|---------|--------------|----------|
| `ComponentBenchmark` | `benchWarmupClassicDebug` | 783.422ms | 25.16 MB |
| `ComponentBenchmark` | `benchWarmupSdcDebug` | 773.735ms | 33.25 MB |
| `ComponentBenchmark` | `benchWarmupClassic` | 573.221ms | 23.13 MB |
| `ComponentBenchmark` | `benchWarmupSdc` | 596.464ms | 31.47 MB |
| `ComponentBenchmark` | `benchRenderClassic` | 26.523ms | 31.63 MB |
| `ComponentBenchmark` | `benchRenderSdc` | 27.191ms | 36.24 MB |
| `ComponentBenchmark` | `benchRenderSdcDev` | 68.700ms | 90.88 MB |
| `ComponentBenchmark` | `benchRenderSdcDevRepeated` | 49.723ms | 47.72 MB |

**Evaluation:**
- **Warmup (Cold Boot):** In the `dev` environment (Debug), the difference between Classic and SDC is minimal. In `prod` (Warmup Sdc), the overhead of container compilation for 500 components is around 15ms.
- **Memory:** The SDC approach has approximately 8MB higher memory peak during container build, which is expected due to the registration of metadata for 500 components.
- **Render (Runtime - Prod):** After optimization (removing `md5` and reducing event listener overhead), the difference in rendering 500 components in production is practically zero (within margin of error).
- **Render (Runtime - Dev):**
    - **Unique Components:** In `dev` mode, rendering **500 unique components** takes about **68.7ms**. This overhead (~42ms for 500 components, or **84µs per component**) is caused by runtime autodiscovery (scanning the file system for each unique component).
    - **Repeated Components:** When rendering **500 components with only 10 unique ones**, the time drops to **49.7ms**. This demonstrates that the internal metadata cache is effective and significantly reduces the overhead once a component has been discovered once during the request.
- **Caching:** Implemented runtime caching in `ComponentMetadataResolver` and `DevComponentRenderListener` ensures that each component class is analyzed only once during a request.

