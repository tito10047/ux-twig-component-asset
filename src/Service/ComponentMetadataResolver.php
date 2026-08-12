<?php

/*
 * This file is part of the UX SDC Bundle
 *
 * (c) Jozef Môstka <https://github.com/tito10047/ux-sdc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tito10047\UX\Sdc\Service;

use ReflectionClass;
use Tito10047\UX\Sdc\Attribute\AsSdcComponent;
use Tito10047\UX\Sdc\Attribute\SdcAsset;

class ComponentMetadataResolver
{
    private array $resolvedRoots = [];
    private array $classCache = [];

    public function __construct(
        private array $twigRoots,
        private bool $autoDiscoveryEnabled
    ) {
        foreach ($this->twigRoots as $root) {
            $realRoot = realpath($root);
            if ($realRoot) {
                $this->resolvedRoots[] = $realRoot . DIRECTORY_SEPARATOR;
            }
        }
    }

    public function resolveMetadata(string $class, string $componentName, array &$allMetadata = []): array
    {
        if (isset($this->classCache[$class])) {
            $cached = $this->classCache[$class];
            if (isset($cached['template'])) {
                $allMetadata[$componentName . '_template'] = $cached['template'];
            }
            return $cached['assets'];
        }

        if (!class_exists($class)) {
            return [];
        }

        $reflectionClass = new ReflectionClass($class);
        $assets = $this->collectExplicitAssets($reflectionClass);
        $template = null;

        if ($this->autoDiscoveryEnabled) {
            $dir = dirname($reflectionClass->getFileName());
            $baseName = $reflectionClass->getShortName();
            $basePath = $dir . DIRECTORY_SEPARATOR . $baseName;

            foreach (['css', 'js'] as $ext) {
                $file = $basePath . '.' . $ext;
                if (file_exists($file)) {
                    $realFile = realpath($file);
                    $shortestPath = $this->findShortestRelativePath($realFile ?: $file);
                    $assets[] = [
                        'path' => $shortestPath ?: ($baseName . '.' . $ext),
                        'type' => $ext,
                        'priority' => 0,
                        'attributes' => [],
                    ];
                }
            }

            $twigFile = $basePath . '.html.twig';
            if (file_exists($twigFile)) {
                $realTwigFile = realpath($twigFile);
                $shortestPath = $this->findShortestRelativePath($realTwigFile ?: $twigFile);
                if ($shortestPath) {
                    $template = $shortestPath;
                    $allMetadata[$componentName . '_template'] = $shortestPath;
                }
            }
        }

        $this->classCache[$class] = [
            'assets' => $assets,
            'template' => $template,
        ];

        return $assets;
    }

    private function collectExplicitAssets(ReflectionClass $reflectionClass): array
    {
        $attributes = $reflectionClass->getAttributes();
        if (empty($attributes)) {
            return [];
        }

        $assets = [];

        foreach ($attributes as $attribute) {
            $attributeName = $attribute->getName();

            if ($attributeName === AsSdcComponent::class) {
                /** @var AsSdcComponent $sdcComponent */
                $sdcComponent = $attribute->newInstance();
                if ($sdcComponent->path) {
                    $assets[] = [
                        'path' => $sdcComponent->path,
                        'type' => $sdcComponent->type ?? '',
                        'priority' => $sdcComponent->priority,
                        'attributes' => $sdcComponent->attributes,
                    ];
                }
            }

            if ($attributeName === SdcAsset::class) {
                /** @var SdcAsset $sdcAsset */
                $sdcAsset = $attribute->newInstance();
                if ($sdcAsset->path) {
                    $assets[] = [
                        'path' => $sdcAsset->path,
                        'type' => $sdcAsset->type ?? '',
                        'priority' => $sdcAsset->priority,
                        'attributes' => $sdcAsset->attributes,
                    ];
                }
            }
        }

        return $assets;
    }


    private function findShortestRelativePath(string $filePath): ?string
    {
        $shortestPath = null;
        foreach ($this->resolvedRoots as $root) {
            if (str_starts_with($filePath, $root)) {
                $relativePath = substr($filePath, strlen($root));
                if ($shortestPath === null || strlen($relativePath) < strlen($shortestPath)) {
                    $shortestPath = $relativePath;
                }
            }
        }

        return $shortestPath;
    }
}
