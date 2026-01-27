### Benchmark Results

#### Branch: `main` (Optimized)
| Benchmark | Subject | Mo (Mean) | Mem Peak |
|-----------|---------|--------------|----------|
| `ComponentBenchmark` | `benchWarmupClassicDebug` | 783.422ms | 25.16 MB |
| `ComponentBenchmark` | `benchWarmupSdcDebug` | 773.735ms | 33.25 MB |
| `ComponentBenchmark` | `benchWarmupClassic` | 573.221ms | 23.13 MB |
| `ComponentBenchmark` | `benchWarmupSdc` | 596.464ms | 31.47 MB |
| `ComponentBenchmark` | `benchRenderClassic` | 26.169ms | 31.63 MB |
| `ComponentBenchmark` | `benchRenderSdc` | 26.965ms | 36.24 MB |

**Evaluation:**
- **Warmup (Cold Boot):** In the `dev` environment (Debug), the difference between Classic and SDC is minimal. In `prod` (Warmup Sdc), the overhead of container compilation for 500 components is around 23ms.
- **Memory:** The SDC approach has approximately 8MB higher memory peak during container build, which is expected due to the registration of metadata for 500 components.
- **Render (Runtime):** After optimization (removing `md5` and reducing event listener overhead), the difference in rendering 500 components is reduced to approximately **0.8ms** (~1.6µs per component), which is practically negligible.
- **Caching:** Implemented runtime caching in `DevComponentRenderListener` ensures that each component is analyzed only once during a request.

