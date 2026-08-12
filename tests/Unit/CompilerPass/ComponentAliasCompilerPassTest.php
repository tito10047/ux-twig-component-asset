<?php

/*
 * This file is part of the UX SDC Bundle
 *
 * (c) Jozef Môstka <https://github.com/tito10047/ux-sdc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tito10047\UX\Sdc\Tests\Unit\CompilerPass;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Tito10047\UX\Sdc\CompilerPass\ComponentAliasCompilerPass;

final class ComponentAliasCompilerPassTest extends TestCase
{
    private function makeContainer(?string $namespace): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setParameter('ux_sdc.component_namespace', $namespace);

        return $container;
    }

    private function addComponent(ContainerBuilder $container, string $class, array $tag = []): void
    {
        $definition = new Definition($class);
        $definition->addTag('twig.component', $tag);
        $container->setDefinition('svc.'.$class, $definition);
    }

    private function getTags(ContainerBuilder $container, string $class): array
    {
        return $container->getDefinition('svc.'.$class)->getTag('twig.component');
    }

    public function testAddsShortAliasWhenLastTwoSegmentsMatch(): void
    {
        $container = $this->makeContainer('TestNs\\');
        $this->addComponent($container, 'TestNs\\Alert\\Alert');

        (new ComponentAliasCompilerPass())->process($container);

        $tags = $this->getTags($container, 'TestNs\\Alert\\Alert');
        $this->assertCount(2, $tags);
        $this->assertFalse(array_key_exists('key', $tags[0]), 'Original tag must remain without explicit key');
        $this->assertSame('Alert', $tags[1]['key']);
    }

    public function testHandlesDeepPath(): void
    {
        $container = $this->makeContainer('TestNs\\');
        $this->addComponent($container, 'TestNs\\UI\\Alert\\Alert');

        (new ComponentAliasCompilerPass())->process($container);

        $tags = $this->getTags($container, 'TestNs\\UI\\Alert\\Alert');
        $this->assertCount(2, $tags);
        $this->assertSame('UI:Alert', $tags[1]['key']);
    }

    public function testNoAliasWhenSegmentsDiffer(): void
    {
        $container = $this->makeContainer('TestNs\\');
        $this->addComponent($container, 'TestNs\\UI\\Alert');

        (new ComponentAliasCompilerPass())->process($container);

        $tags = $this->getTags($container, 'TestNs\\UI\\Alert');
        $this->assertCount(1, $tags);
    }

    public function testNoAliasWhenExplicitKeySet(): void
    {
        $container = $this->makeContainer('TestNs\\');
        $this->addComponent($container, 'TestNs\\Alert\\Alert', ['key' => 'CustomName']);

        (new ComponentAliasCompilerPass())->process($container);

        $tags = $this->getTags($container, 'TestNs\\Alert\\Alert');
        $this->assertCount(1, $tags);
        $this->assertSame('CustomName', $tags[0]['key']);
    }

    public function testNoAliasWhenNamespaceNotSet(): void
    {
        $container = $this->makeContainer(null);
        $this->addComponent($container, 'TestNs\\Alert\\Alert');

        (new ComponentAliasCompilerPass())->process($container);

        $tags = $this->getTags($container, 'TestNs\\Alert\\Alert');
        $this->assertCount(1, $tags);
    }

    public function testNoAliasWhenClassOutsideNamespace(): void
    {
        $container = $this->makeContainer('App\\');
        $this->addComponent($container, 'OtherNs\\Alert\\Alert');

        (new ComponentAliasCompilerPass())->process($container);

        $tags = $this->getTags($container, 'OtherNs\\Alert\\Alert');
        $this->assertCount(1, $tags);
    }
}
