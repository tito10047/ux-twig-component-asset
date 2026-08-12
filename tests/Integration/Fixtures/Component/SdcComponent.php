<?php

/*
 * This file is part of the UX SDC Bundle
 *
 * (c) Jozef Môstka <https://github.com/tito10047/ux-sdc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tito10047\UX\Sdc\Tests\Integration\Fixtures\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Tito10047\UX\Sdc\Attribute\SdcAsset;

#[AsTwigComponent('SdcComponent', template: 'components/SdcComponent.html.twig')]
#[SdcAsset(path: 'css/sdc.css', type: 'css')]
#[SdcAsset(path: 'js/sdc.js', type: 'js')]
class SdcComponent
{
}
