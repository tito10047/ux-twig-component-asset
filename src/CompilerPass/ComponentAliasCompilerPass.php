<?php

/*
 * This file is part of the UX SDC Bundle
 *
 * (c) Jozef Môstka <https://github.com/tito10047/ux-sdc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tito10047\UX\Sdc\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Registers a short alias for components whose directory name matches the class name.
 *
 * Example: App\Alert\Alert (auto-name "Alert:Alert") → also registered as "Alert",
 * so <twig:Alert/> works alongside <twig:Alert:Alert/>.
 */
final class ComponentAliasCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('ux_sdc.component_namespace')) {
            return;
        }

        $namespace = $container->getParameter('ux_sdc.component_namespace');

        if (null === $namespace) {
            return;
        }

        foreach ($container->findTaggedServiceIds('twig.component') as $id => $tags) {
            $definition = $container->findDefinition($id);
            $fqcn = $definition->getClass() ?? '';

            foreach ($tags as $tag) {
                if (array_key_exists('key', $tag)) {
                    continue;
                }

                if (!str_starts_with($fqcn, $namespace)) {
                    continue;
                }

                $name = str_replace('\\', ':', substr($fqcn, \strlen($namespace)));
                $parts = explode(':', $name);

                if (\count($parts) >= 2 && end($parts) === $parts[\count($parts) - 2]) {
                    $shortName = implode(':', \array_slice($parts, 0, -1));
                    $definition->addTag('twig.component', ['key' => $shortName]);
                }
            }
        }
    }
}
