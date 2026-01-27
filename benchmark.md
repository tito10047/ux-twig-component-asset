### Benchmark Results

#### Branch: `main` (Optimized with Stimulus Auto-discovery)
| Benchmark | Subject | Mo (Mean) | Mem Peak |
|-----------|---------|--------------|----------|
| `ComponentBenchmark` | `benchWarmupClassicDebug` | 809.768ms | 25.01 MB |
| `ComponentBenchmark` | `benchWarmupSdcDebug` | 781.969ms | 34.04 MB |
| `ComponentBenchmark` | `benchWarmupClassic` | 583.126ms | 23.08 MB |
| `ComponentBenchmark` | `benchWarmupSdc` | 586.228ms | 32.28 MB |
| `ComponentBenchmark` | `benchRenderClassic` | 26.523ms | 31.63 MB |
| `ComponentBenchmark` | `benchRenderSdc` | 31.621ms | 45.02 MB |
| `ComponentBenchmark` | `benchRenderSdcDev` | 88.408ms | 105.29 MB |
| `ComponentBenchmark` | `benchRenderSdcDevRepeated` | 58.000ms | 58.52 MB |

**Evaluation:**
- **Warmup (Cold Boot):** Container compilation time remains very efficient. Sdc approach in `prod` shows almost identical results to Classic for 500 components.
- **Memory:** The Stimulus auto-discovery feature adds some memory overhead. In `prod` build, peak memory increased to ~32MB. During rendering, SDC uses about 13MB more than Classic due to metadata and Stimulus controller name calculations.
- **Render (Runtime - Prod):** Rendering 500 components with automatic Stimulus controller discovery takes **31.6ms**. Compared to the previous version without this feature (~27.2ms), there is an overhead of about **4.4ms for 500 components** (approx. **8.8µs per component**). This is a very small price to pay for the automated Stimulus integration.
- **Render (Runtime - Dev):**
    - **Unique Components:** Rendering 500 unique components in `dev` mode takes **88.4ms**. The overhead compared to previous version (~68.7ms) is around **20ms**, reflecting the cost of reflection and path analysis needed for Stimulus auto-discovery.
    - **Repeated Components:** For 500 components with 10 unique ones, the time is **58ms**, confirming that caching is still effective.
- **Stimulus Integration:** The new feature allows components to automatically know their Stimulus controller name based on their file system location. The performance impact is minimal in production and acceptable in development.

