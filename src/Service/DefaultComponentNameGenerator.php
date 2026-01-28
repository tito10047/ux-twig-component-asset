<?php

namespace Tito10047\UX\Sdc\Service;

class DefaultComponentNameGenerator implements ComponentNameGeneratorInterface
{
    public function __construct(
        private ?string $componentNamespace = null,
        private string $separator = ':',
        private bool $lowercase = true,
    ) {
    }

    public function generateName(string $componentClass): ?string
    {
        if (null === $this->componentNamespace) {
            return null;
        }

        if (!str_starts_with($componentClass, $this->componentNamespace)) {
            return null;
        }

        $relativeClass = substr($componentClass, strlen($this->componentNamespace));
        $name = str_replace('\\', $this->separator, $relativeClass);

        if ($this->lowercase) {
            $name = strtolower($name);
        }

        return $name;
    }
}
