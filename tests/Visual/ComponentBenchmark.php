<?php

namespace Tito10047\UX\TwigComponentSdc\Tests\Visual;

use Tito10047\UX\TwigComponentSdc\Tests\Visual\ComponentGenerator\ComponentGenerator;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;

/**
 * @BeforeMethods({"prepare"})
 */
class ComponentBenchmark
{
    public function prepare(): void
    {
        $generator = new ComponentGenerator();
        $generator->generate(__DIR__ . '/Generated/Classic', 500, false);
        $generator->generate(__DIR__ . '/Generated/Sdc', 500, true);
    }

    /**
     * @Revs(5)
     * @Iterations(3)
     */
    public function benchWarmupClassic(): void
    {
        $kernel = new BenchmarkKernel('classic');
        $kernel->boot();
        $container = $kernel->getContainer();
        // Force compilation/cache warmup if needed, though boot() should do much of it
    }

    /**
     * @Revs(5)
     * @Iterations(3)
     */
    public function benchWarmupSdc(): void
    {
        $kernel = new BenchmarkKernel('sdc');
        $kernel->boot();
    }

    /**
     * @Revs(10)
     * @Iterations(5)
     */
    public function benchRenderClassic(): void
    {
        $kernel = new BenchmarkKernel('classic');
        $kernel->boot();
        $twig = $kernel->getContainer()->get('twig');
        
        $template = '';
        for ($i = 1; $i <= 500; $i++) {
            $template .= "{{ component('ClassicComp$i') }}";
        }

        $twig->createTemplate($template)->render();
    }

    /**
     * @Revs(10)
     * @Iterations(5)
     */
    public function benchRenderSdc(): void
    {
        $kernel = new BenchmarkKernel('sdc');
        $kernel->boot();
        $container = $kernel->getContainer();
        
        $twig = $container->get('twig');
        
        $template = '';
        for ($i = 1; $i <= 500; $i++) {
            $template .= "{{ component('SdcComp$i') }}";
        }

        $twig->createTemplate($template)->render();
    }
}
