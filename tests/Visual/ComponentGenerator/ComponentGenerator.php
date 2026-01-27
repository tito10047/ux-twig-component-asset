<?php

namespace Tito10047\UX\Sdc\Tests\Visual\ComponentGenerator;

class ComponentGenerator
{
    public function generate(string $baseDir, int $count, bool $isSdc): void
    {
        $dirName = $isSdc ? 'Sdc' : 'Classic';
        $fullDir = $baseDir . '/' . $dirName;
        if (!is_dir($fullDir)) {
            mkdir($fullDir, 0777, true);
        }

        $namespace = "Tito10047\\UX\\Sdc\\Tests\\Visual\\Generated\\" . $dirName."\\".$dirName;
        $classNamePrefix = $isSdc ? "Sdc" : "Classic";

        for ($i = 1; $i <= $count; $i++) {
            $name = $classNamePrefix . "Comp" . $i;
            $this->generateComponent($fullDir, $name, $namespace, $isSdc);
        }
    }

    private function generateComponent(string $dir, string $name, string $namespace, bool $isSdc): void
    {
        if ($isSdc) {
            $phpContent = <<<PHP
<?php

namespace $namespace;

use Tito10047\UX\Sdc\Attribute\AsSdcComponent;
use Tito10047\UX\Sdc\Twig\ComponentNamespaceInterface;
use Tito10047\UX\Sdc\Twig\Stimulus;

#[AsSdcComponent('$name')]
class $name implements ComponentNamespaceInterface
{
    use Stimulus;
}
PHP;
        } else {
            $phpContent = <<<PHP
<?php

namespace $namespace;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('$name')]
class $name
{
}
PHP;
        }
        
        file_put_contents($dir . "/" . $name . ".php", $phpContent);

        if ($isSdc) {
            $twigContent = "<div data-controller='{{ controller }}'>$name content</div>";
        } else {
            $twigContent = "<div>$name content</div>";
        }
        file_put_contents($dir . "/" . $name . ".html.twig", $twigContent);

        if ($isSdc) {
            file_put_contents($dir . "/" . $name . ".css", ".{$name} { color: red; }");
            file_put_contents($dir . "/" . $name . ".js", "console.log('{$name}');");
        }
    }
}
