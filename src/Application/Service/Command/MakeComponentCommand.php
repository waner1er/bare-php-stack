<?php

declare(strict_types=1);

namespace App\Application\Service\Command;

use App\Application\Service\Command\CommandInterface;

class MakeComponentCommand implements CommandInterface
{
    public function execute(?string $name, array $options = []): void
    {
        if (!$name) {
            echo "Usage: minor make:component ComponentName [--class]\n";
            exit(1);
        }

        $createClass = in_array('--class', $options);

        $componentName = strtolower($name);
        $className = ucfirst($name);

        $componentsDir = VIEW_PATH . '/components';
        $filePath = $componentsDir . "/{$componentName}.blade.php";

        if (!is_dir($componentsDir)) {
            mkdir($componentsDir, 0755, true);
        }

        if (file_exists($filePath)) {
            echo "Le composant {$componentName} existe déjà.\n";
            exit(1);
        }

        // Template de base pour le composant
        $template = <<<'BLADE'
{{-- Component: {componentName} --}}
<div>
    <!--  -->
</div>

BLADE;

        $content = str_replace('{componentName}', $componentName, $template);

        file_put_contents($filePath, $content);
        echo "✓ Composant Blade '{$componentName}' créé dans resources/views/components/\n";

        if ($createClass) {
            $componentClassDir = VIEW_PATH . '/components';
            $componentClassPath = $componentClassDir . "/{$className}.php";

            if (!is_dir($componentClassDir)) {
                mkdir($componentClassDir, 0755, true);
            }

            if (file_exists($componentClassPath)) {
                echo "⚠ La classe Component\\{$className} existe déjà.\n";
                return;
            }

            $classTemplate = <<<PHP
<?php

namespace App\Component;

use App\Infrastructure\Blade;

class {$className}
{
    public function __construct(array \$data = [])
    {
        // Initialiser les propriétés du composant
    }

    public function render(): string
    {
        \$views = VIEW_PATH;
        \$cache = CACHE_PATH;
        \$blade = new Blade(\$views, \$cache);
        
        return \$blade->render('components.{$componentName}', [
            // Passer les données au template
        ]);
    }
}

PHP;

            file_put_contents($componentClassPath, $classTemplate);
            echo "✓ Classe Component\\{$className} créée dans src/Component/\n";
        }
    }
}
