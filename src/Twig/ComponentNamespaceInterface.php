<?php

namespace Tito10047\UX\Sdc\Twig;

interface ComponentNamespaceInterface
{
    public function setComponentNamespace(string $namespace): void;
    public function getController(): string ;
}
