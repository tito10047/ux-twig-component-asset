<?php

namespace Integration;

use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\AssetMapper\MappedAsset;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Tito10047\UX\Sdc\EventListener\AssetResponseListener;
use Tito10047\UX\Sdc\Service\AssetRegistry;
use Tito10047\UX\Sdc\Tests\Integration\IntegrationTestCase;
use Twig\Environment;

class DevComponentRenderListenerTest extends IntegrationTestCase
{
    public function testAutoNameGeneration(): void
    {
        $kernel = self::bootKernel(['configs' => ['auto_discovery' => false], 'environment' => 'dev']);
        $container = self::getContainer();



        /** @var Environment $twig */
        $twig = $container->get(Environment::class);

        $html = $twig->render('name_generated.html.twig');

		$this->assertNull($html);

    }

}
