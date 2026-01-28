<?php

namespace Tito10047\UX\Sdc\Service;

interface ComponentNameGeneratorInterface
{
    public function generateName(string $componentClass): ?string;
}
