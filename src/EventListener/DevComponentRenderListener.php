<?php

namespace Tito10047\UX\TwigComponentSdc\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\UX\TwigComponent\Event\PreRenderEvent;
use Tito10047\UX\TwigComponentSdc\Service\AssetRegistry;
use Tito10047\UX\TwigComponentSdc\Service\ComponentMetadataResolver;

final class DevComponentRenderListener
{
    private array $runtimeMetadata = [];

    public function __construct(
        private ComponentMetadataResolver $metadataResolver,
        private AssetRegistry $assetRegistry
    ) {
    }

    #[AsEventListener(event: PreRenderEvent::class)]
    public function onPreRender(PreRenderEvent $event): void
    {
        $metadata = $event->getMetadata();
        $componentName = $metadata->getName();

        if (isset($this->runtimeMetadata[$componentName])) {
            $assets = $this->runtimeMetadata[$componentName];
        } else {
            $componentClass = $metadata->getClass();
            $assets = $this->metadataResolver->resolveMetadata($componentClass, $componentName, $this->runtimeMetadata);
            $this->runtimeMetadata[$componentName] = $assets;
        }

        foreach ($assets as $asset) {
            $type = $asset['type'];
            if ('' === $type) {
                $type = str_ends_with($asset['path'], '.css') ? 'css' : 'js';
            }

            $this->assetRegistry->addAsset(
                $asset['path'],
                $type,
                $asset['priority'],
                $asset['attributes']
            );
        }

        $templatePath = $this->runtimeMetadata[$componentName . '_template'] ?? null;
        if (is_string($templatePath)) {
            $event->setTemplate($templatePath);
        }
    }
}
