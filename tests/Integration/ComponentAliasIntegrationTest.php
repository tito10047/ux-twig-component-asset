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

use Twig\Environment;

/**
 * Tests that a component whose directory name matches its class name is reachable
 * via both its full name (Alert:Alert) and its short alias (Alert).
 */
class ComponentAliasIntegrationTest extends IntegrationTestCase
{
    public function testShortAliasRendersIdenticallyToFullName(): void
    {
        self::bootKernel();
        /** @var Environment $twig */
        $twig = self::getContainer()->get(Environment::class);

        $short = trim($twig->render('component_alias_short.html.twig'));
        $long  = trim($twig->render('component_alias_long.html.twig'));

        $this->assertStringContainsString('data-testid="alert-component"', $short);
        $this->assertSame($long, $short, 'Short alias "Alert" must render identically to full name "Alert:Alert"');
    }

    public function testShortAliasContainsExpectedContent(): void
    {
        self::bootKernel();
        /** @var Environment $twig */
        $twig = self::getContainer()->get(Environment::class);

        $html = $twig->render('component_alias_short.html.twig');

        $this->assertStringContainsString('Alert Component Content', $html);
    }

    public function testDeepPathShortAliasRendersCorrectly(): void
    {
        self::bootKernel();
        /** @var Environment $twig */
        $twig = self::getContainer()->get(Environment::class);

        $html = $twig->render('component_alias_ui_short.html.twig');

        $this->assertStringContainsString('data-testid="ui-alert-component"', $html);
        $this->assertStringContainsString('UI Alert Component Content', $html);
    }
}
