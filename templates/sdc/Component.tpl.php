<?php

/*
 * This file is part of the UX SDC Bundle
 *
 * (c) Jozef Môstka <https://github.com/tito10047/ux-sdc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

?>
<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Tito10047\UX\Sdc\Attribute\AsSdcComponent;
<?php if ($with_stimulus): ?>
use Tito10047\UX\Sdc\Twig\ComponentNamespaceInterface;
use Tito10047\UX\Sdc\Twig\Stimulus;
<?php endif; ?>

#[AsTwigComponent]
#[AsSdcComponent]
class <?= $class_name ?><?php if ($with_stimulus): ?> implements ComponentNamespaceInterface<?php endif; ?>

{
<?php if ($with_stimulus): ?>
    use Stimulus;
<?php endif; ?>
}
