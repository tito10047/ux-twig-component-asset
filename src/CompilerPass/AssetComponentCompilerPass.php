<?php

namespace Tito10047\UX\TwigComponentSdc\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Tito10047\UX\TwigComponentSdc\Attribute\Asset;
use Tito10047\UX\TwigComponentSdc\Attribute\AsSdcComponent;
use Tito10047\UX\TwigComponentSdc\Runtime\SdcMetadataRegistry;
use ReflectionClass;

final class AssetComponentCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(SdcMetadataRegistry::class)) {
            return;
        }

        $registryDefinition = $container->getDefinition(SdcMetadataRegistry::class);
        $cachePath = $container->getParameterBag()->resolveValue($registryDefinition->getArgument('$cachePath'));

        $env = $container->hasParameter('kernel.environment') ? $container->getParameter('kernel.environment') : 'test';
        if ($env === 'dev') {
            if (file_exists($cachePath)) {
                unlink($cachePath);
            }

            return;
        }

        $autoDiscovery = $container->getParameter('twig_component_sdc.auto_discovery');
        $twigRoots = $this->collectTwigRoots($container);
        $componentAssets = $this->processTaggedServices($container, $autoDiscovery, $twigRoots);

        $this->dumpCache($cachePath, $componentAssets);
    }

    private function collectTwigRoots(ContainerBuilder $container): array
    {
        $twigRoots = [];
        if ($container->hasParameter('kernel.project_dir')) {
            $defaultDir = $container->getParameterBag()->resolveValue('%kernel.project_dir%/src_component');
            if (is_dir($defaultDir)) {
                $twigRoots[] = realpath($defaultDir);
            }
        }

        if ($container->hasParameter('twig.default_path')) {
            $twigRoots[] = $container->getParameterBag()->resolveValue($container->getParameter('twig.default_path'));
        }

        if ($container->hasParameter('twig_component_sdc.ux_components_dir')) {
            $uxDir = $container->getParameterBag()->resolveValue($container->getParameter('twig_component_sdc.ux_components_dir'));
            if (is_dir($uxDir)) {
                $twigRoots[] = realpath($uxDir);
            }
        }

        // Spracovanie twig paths
        if ($container->hasExtension('twig')) {
            $twigConfigs = $container->getExtensionConfig('twig');
            foreach ($twigConfigs as $config) {
                if (isset($config['paths'])) {
                    foreach ($config['paths'] as $path => $namespace) {
                        $actualPath = is_array($path) ? key($path) : $path;
                        if (is_numeric($actualPath)) {
                            $actualPath = $namespace;
                        }
                        $twigRoots[] = $container->getParameterBag()->resolveValue($actualPath);
                    }
                }
            }
        }

        return array_unique(array_map('realpath', array_filter($twigRoots)));
    }

    private function processTaggedServices(ContainerBuilder $container, bool $autoDiscovery, array $twigRoots): array
    {
        $componentAssets = [];
        $taggedServices = $container->findTaggedServiceIds('twig.component');

        foreach ($taggedServices as $id => $tags) {
            $definition = $container->getDefinition($id);
            $class = $definition->getClass();

            if (!$class || !class_exists($class)) {
                continue;
            }

            $componentName = null;
            foreach ($tags as $tag) {
                if (isset($tag['key'])) {
                    $componentName = $tag['key'];
                    break;
                }
            }

            if (!$componentName) {
                continue;
            }

            $reflectionClass = new ReflectionClass($class);
            $assets = $this->collectExplicitAssets($reflectionClass);

            if ($autoDiscovery) {
                $assets = array_merge($assets, $this->performAutoDiscovery($reflectionClass, $twigRoots, $componentName, $componentAssets));
            }

            if (!empty($assets)) {
                $componentAssets[$componentName] = $assets;
            }
        }

        return $componentAssets;
    }

    private function collectExplicitAssets(ReflectionClass $reflectionClass): array
    {
        $assets = [];

        // 1. Čítanie atribútov #[Asset]
        foreach ($reflectionClass->getAttributes(Asset::class) as $attribute) {
            /** @var Asset $asset */
            $asset = $attribute->newInstance();
            if ($asset->path) {
                $assets[] = [
                    'path' => $asset->path,
                    'type' => $asset->type ?? '',
                    'priority' => $asset->priority,
                    'attributes' => $asset->attributes,
                ];
            }
        }

        // 1b. Čítanie atribútu #[AsSdcComponent]
        foreach ($reflectionClass->getAttributes(AsSdcComponent::class) as $attribute) {
            /** @var AsSdcComponent $sdcComponent */
            $sdcComponent = $attribute->newInstance();

            if ($sdcComponent->css) {
                $assets[] = [
                    'path' => $sdcComponent->css,
                    'type' => 'css',
                    'priority' => 0,
                    'attributes' => [],
                ];
            }

            if ($sdcComponent->js) {
                $assets[] = [
                    'path' => $sdcComponent->js,
                    'type' => 'js',
                    'priority' => 0,
                    'attributes' => [],
                ];
            }
        }

        return $assets;
    }

    private function performAutoDiscovery(ReflectionClass $reflectionClass, array $twigRoots, string $componentName, array &$componentAssets): array
    {
        $assets = [];
        $dir = dirname($reflectionClass->getFileName());
        $baseName = $reflectionClass->getShortName();

        // CSS a JS auto-discovery
        foreach (['css', 'js'] as $ext) {
            $assetFile = realpath($dir . DIRECTORY_SEPARATOR . $baseName . '.' . $ext);
            if ($assetFile && file_exists($assetFile)) {
                $shortestPath = $this->findShortestRelativePath($assetFile, $twigRoots);
                $assets[] = [
                    'path' => $shortestPath ?: ($baseName . '.' . $ext),
                    'type' => $ext,
                    'priority' => 0,
                    'attributes' => [],
                ];
            }
        }

        // Twig template auto-discovery
        $twigFile = realpath($dir . DIRECTORY_SEPARATOR . $baseName . '.html.twig');
        if ($twigFile && file_exists($twigFile)) {
            $shortestPath = $this->findShortestRelativePath($twigFile, $twigRoots);
            if ($shortestPath) {
                $componentAssets[$componentName . '_template'] = $shortestPath;
            }
        }

        return $assets;
    }

    private function findShortestRelativePath(string $filePath, array $roots): ?string
    {
        $shortestPath = null;
        foreach ($roots as $root) {
            if (str_starts_with($filePath, $root)) {
                $relativePath = ltrim(substr($filePath, strlen($root)), DIRECTORY_SEPARATOR);
                if ($shortestPath === null || strlen($relativePath) < strlen($shortestPath)) {
                    $shortestPath = $relativePath;
                }
            }
        }

        return $shortestPath;
    }

    private function dumpCache(string $path, array $data): void
    {
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $content = '<?php return ' . var_export($data, true) . ';';
        file_put_contents($path, $content);
    }
}
