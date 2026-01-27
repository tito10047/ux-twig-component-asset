<?php

namespace Tito10047\UX\TwigComponentSdc\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\UX\TwigComponent\Event\PreCreateForRenderEvent;
use Symfony\UX\TwigComponent\Event\PreRenderEvent;
use Tito10047\UX\TwigComponentSdc\Runtime\SdcMetadataRegistry;
use Tito10047\UX\TwigComponentSdc\Service\AssetRegistry;

final class ComponentRenderListener
{
    public function __construct(
        private SdcMetadataRegistry $metadataRegistry,
        private AssetRegistry $assetRegistry
    ) {
    }

    #[AsEventListener(event: PreCreateForRenderEvent::class)]
    public function onPreCreate(PreCreateForRenderEvent $event): void
    {
        $componentName = $event->getName();
        $assets = $this->metadataRegistry->getMetadata($componentName);

        if (!$assets) {
            return;
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
    }

    #[AsEventListener(event: PreRenderEvent::class)]
    public function onPreRender(PreRenderEvent $event): void
    {
        $componentName = $event->getMetadata()->getName();
        $templatePath = $this->metadataRegistry->getMetadata($componentName . '_template');

        if ($templatePath) {
            $event->setTemplate($templatePath);
        }
    }
}
