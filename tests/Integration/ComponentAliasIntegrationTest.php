<?php

/*
 * This file is part of the UX SDC Bundle
 *
 * (c) Jozef Môstka <https://github.com/tito10047/ux-sdc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tito10047\UX\Sdc\Tests\Integration;

use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\AssetMapper\MappedAsset;
use Twig\Environment;

/**
 * Tests that a component whose directory name matches its class name is reachable
 * via both its full name (Alert:Alert) and its short alias (Alert).
 */
class ComponentAliasIntegrationTest extends IntegrationTestCase
{
    public function testShortAliasRendersIdenticallyToFullName(): void
    {
		$kernel = self::bootKernel(['configs' => ['auto_discovery' => true], 'environment' => 'dev']);
		$container = self::getContainer();

		$assetMapper = $this->createMock(AssetMapperInterface::class);
		$assetMapper->method('getAsset')
			->willReturnCallback(function ($path) {
				return new MappedAsset($path, publicPath: '/assets/'.$path);
			});

		$container->set(AssetMapperInterface::class, $assetMapper);

		/** @var Environment $twig */
		$twig = $container->get(Environment::class);

		$loader = $twig->getLoader();
		if ($loader instanceof \Twig\Loader\FilesystemLoader) {
			$loader->addPath(realpath(__DIR__ . '/Fixtures/Component'));
		}


		$long  = trim($twig->render('component_alias_long.html.twig'));
		$short = trim($twig->render('component_alias_short.html.twig'));

        $this->assertStringContainsString('data-testid="alert-component"', $short);
        $this->assertSame($long, $short, 'Short alias "Alert" must render identically to full name "Alert:Alert"');
    }

    public function testShortAliasContainsExpectedContent(): void
    {
		$kernel = self::bootKernel(['configs' => ['auto_discovery' => true], 'environment' => 'dev']);
		$container = self::getContainer();

		$assetMapper = $this->createMock(AssetMapperInterface::class);
		$assetMapper->method('getAsset')
			->willReturnCallback(function ($path) {
				return new MappedAsset($path, publicPath: '/assets/'.$path);
			});

		$container->set(AssetMapperInterface::class, $assetMapper);

		/** @var Environment $twig */
		$twig = $container->get(Environment::class);

		$loader = $twig->getLoader();
		if ($loader instanceof \Twig\Loader\FilesystemLoader) {
			$loader->addPath(realpath(__DIR__ . '/Fixtures/Component'));
		}


		$html = $twig->render('component_alias_short.html.twig');

        $this->assertStringContainsString('Short alias "Alert"', $html);
    }

    public function testDeepPathShortAliasRendersCorrectly(): void
    {
		$kernel = self::bootKernel(['configs' => ['auto_discovery' => true], 'environment' => 'dev']);
		$container = self::getContainer();

		$assetMapper = $this->createMock(AssetMapperInterface::class);
		$assetMapper->method('getAsset')
			->willReturnCallback(function ($path) {
				return new MappedAsset($path, publicPath: '/assets/'.$path);
			});

		$container->set(AssetMapperInterface::class, $assetMapper);

		/** @var Environment $twig */
		$twig = $container->get(Environment::class);

		$loader = $twig->getLoader();
		if ($loader instanceof \Twig\Loader\FilesystemLoader) {
			$loader->addPath(realpath(__DIR__ . '/Fixtures/Component'));
		}


		$html = $twig->render('component_alias_ui_short.html.twig');

        $this->assertStringContainsString('data-testid="ui-alert-component"', $html);
        $this->assertStringContainsString('UI Alert Component Content', $html);
    }
}
