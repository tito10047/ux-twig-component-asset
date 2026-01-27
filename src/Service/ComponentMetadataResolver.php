<?php

namespace Tito10047\UX\TwigComponentSdc\Service;

use ReflectionClass;
use Tito10047\UX\TwigComponentSdc\Attribute\Asset;
use Tito10047\UX\TwigComponentSdc\Attribute\AsSdcComponent;

class ComponentMetadataResolver
{
    public function __construct(
        private array $twigRoots,
        private bool $autoDiscoveryEnabled
    ) {
    }

    public function resolveMetadata(string $class, string $componentName, array &$allMetadata = []): array
    {
        if (!class_exists($class)) {
            return [];
        }

        $reflectionClass = new ReflectionClass($class);
        $assets = $this->collectExplicitAssets($reflectionClass);

        if ($this->autoDiscoveryEnabled) {
            $assets = array_merge($assets, $this->performAutoDiscovery($reflectionClass, $componentName, $allMetadata));
        }

        if (empty($assets)) {
            return [];
        }

        return $assets;
    }

    private function collectExplicitAssets(ReflectionClass $reflectionClass): array
    {
        $assets = [];

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

    private function performAutoDiscovery(ReflectionClass $reflectionClass, string $componentName, array &$allMetadata): array
    {
        $assets = [];
        $dir = dirname($reflectionClass->getFileName());
        $baseName = $reflectionClass->getShortName();

        foreach (['css', 'js'] as $ext) {
            $assetFile = realpath($dir . DIRECTORY_SEPARATOR . $baseName . '.' . $ext);
            if ($assetFile && file_exists($assetFile)) {
                $shortestPath = $this->findShortestRelativePath($assetFile);
                $assets[] = [
                    'path' => $shortestPath ?: ($baseName . '.' . $ext),
                    'type' => $ext,
                    'priority' => 0,
                    'attributes' => [],
                ];
            }
        }

        $twigFile = realpath($dir . DIRECTORY_SEPARATOR . $baseName . '.html.twig');
        if ($twigFile && file_exists($twigFile)) {
            $shortestPath = $this->findShortestRelativePath($twigFile);
            if ($shortestPath) {
                $allMetadata[$componentName . '_template'] = $shortestPath;
            }
        }

        return $assets;
    }

    private function findShortestRelativePath(string $filePath): ?string
    {
        $shortestPath = null;
        foreach ($this->twigRoots as $root) {
            if (str_starts_with($filePath, $root)) {
                $relativePath = ltrim(substr($filePath, strlen($root)), DIRECTORY_SEPARATOR);
                if ($shortestPath === null || strlen($relativePath) < strlen($shortestPath)) {
                    $shortestPath = $relativePath;
                }
            }
        }

        return $shortestPath;
    }
}
