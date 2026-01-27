<?php

namespace Tito10047\UX\Sdc\Tests\Integration\Fixtures\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Tito10047\UX\Sdc\Twig\ComponentNamespaceInterface;
use Tito10047\UX\Sdc\Twig\Stimulus;

#[AsTwigComponent('stimulus_component', template: 'components/stimulus_component.html.twig')]
class StimulusComponent implements ComponentNamespaceInterface
{
    use Stimulus;
}
