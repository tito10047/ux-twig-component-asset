<?php

namespace Tito10047\UX\Sdc\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Symfony\UX\TwigComponent\Event\PreCreateForRenderEvent;
use Tito10047\UX\Sdc\EventListener\DevComponentRenderListener;
use Tito10047\UX\Sdc\Service\AssetRegistry;
use Tito10047\UX\Sdc\Service\ComponentMetadataResolver;
use Tito10047\UX\Sdc\Service\ComponentNameGeneratorInterface;

class DevComponentRenderListenerTest extends TestCase
{
    public function testPreCreateForRenderSetsNameIfMissing(): void
    {
        $metadataResolver = new ComponentMetadataResolver([], false);
        $assetRegistry = new AssetRegistry();
        $nameGenerator = $this->createMock(ComponentNameGeneratorInterface::class);

        $listener = new DevComponentRenderListener(
            $metadataResolver,
            $assetRegistry,
            $nameGenerator,
            'App\\Component\\'
        );

        // MyComponent must exist for class_exists to return true
        if (!class_exists('App\\Component\\MyComponent')) {
            eval('namespace App\\Component; class MyComponent {}');
        }

        $event = new PreCreateForRenderEvent('App\\Component\\MyComponent', []);
        
        $nameGenerator->expects($this->once())
            ->method('generateName')
            ->with('App\\Component\\MyComponent')
            ->willReturn('my-component');

        $listener->preCreateForRender($event);

        $this->assertSame('my-component', $event->getName());
    }

    public function testPreCreateForRenderDoesNotChangeIfNameIsNotClass(): void
    {
        $metadataResolver = new ComponentMetadataResolver([], false);
        $assetRegistry = new AssetRegistry();
        $nameGenerator = $this->createMock(ComponentNameGeneratorInterface::class);

        $listener = new DevComponentRenderListener(
            $metadataResolver,
            $assetRegistry,
            $nameGenerator,
            'App\\Component\\'
        );

        $event = new PreCreateForRenderEvent('not-a-class', []);
        
        $nameGenerator->expects($this->never())
            ->method('generateName');

        $listener->preCreateForRender($event);

        $this->assertSame('not-a-class', $event->getName());
    }
}
