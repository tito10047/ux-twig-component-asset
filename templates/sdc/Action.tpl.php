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

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class <?= $class_name ?> extends AbstractController
{
    #[Route('/en', name: 'app.<?= strtolower($component_name) ?>')]
    public function index(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('<?= ($sub_path ? $sub_path.'/' : '').$component_name ?>/<?= $component_name ?>Action.html.twig');
    }
}
