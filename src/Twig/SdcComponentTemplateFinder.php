<?php

namespace Tito10047\UX\Sdc\Twig;

use Symfony\UX\TwigComponent\ComponentTemplateFinderInterface;

/**
 * Decorator for the component template finder to support anonymous SDC components.
 */
final class SdcComponentTemplateFinder implements ComponentTemplateFinderInterface
{
    public function __construct(
        private ComponentTemplateFinderInterface $inner,
    ) {
    }

    public function findAnonymousComponentTemplate(string $name): ?string
    {
        return $this->inner->findAnonymousComponentTemplate($name);
    }
}
