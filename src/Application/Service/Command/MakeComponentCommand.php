<?php

declare(strict_types=1);

namespace App\Application\Service\Command;

use App\Application\Service\Command\Interface\CommandInterface;

class MakeComponentCommand implements CommandInterface
{
    public function execute(?string $name, array $options = []): void
    {
        // Demande du nom du composant si non fourni
        if (empty($name)) {
            echo "Entrez le nom du composant : ";
            $name = trim(fgets(STDIN));
            if (empty($name)) {
                echo "Le nom du composant ne peut pas être vide.\n";
                exit(1);
            }
        }

        // Lecture dynamique des dossiers d'interface
        $interfacePath = defined('INTERFACE_PATH') ? INTERFACE_PATH : __DIR__ . '/../../../Interface';
        $dirs = array_filter(scandir($interfacePath), function ($d) use ($interfacePath) {
            return $d[0] !== '.' && is_dir($interfacePath . '/' . $d) && strtolower($d) !== 'common';
        });
        $dirs = array_values($dirs);
        echo "Choisissez l'interface cible :\n";
        foreach ($dirs as $i => $dir) {
            $num = $i + 1;
            echo "  [$num] $dir\n";
        }
        echo "Votre choix : ";
        $choice = trim(fgets(STDIN));
        $choiceIndex = (int) $choice - 1;
        if (!is_numeric($choice) || !isset($dirs[$choiceIndex])) {
            echo "Choix invalide.\n";
            exit(1);
        }
        $selectedType = $dirs[$choiceIndex];

        $componentName = strtolower($name);
        $className = ucfirst($name);

        $componentsDir = INTERFACE_PATH . "/$selectedType/View/components";
        $filePath = $componentsDir . "/{$componentName}.blade.php";

        if (!is_dir($componentsDir)) {
            mkdir($componentsDir, 0755, true);
        }

        if (file_exists($filePath)) {
            echo "Le composant {$componentName} existe déjà dans $selectedType.\n";
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
        echo "✓ Composant Blade '{$componentName}' créé dans Interface/$selectedType/View/components/\n";

        // Demander si l'utilisateur veut générer une classe
        if (!in_array('--class', $options)) {
            echo "Voulez-vous générer une classe pour ce composant ? (o/n) : ";
            $generateClass = strtolower(trim(fgets(STDIN)));
            if ($generateClass !== 'o' && $generateClass !== 'oui' && $generateClass !== 'y' && $generateClass !== 'yes') {
                exit(0);
            }
        }

        $componentClassDir = INTERFACE_PATH . "/$selectedType/Component";
        $componentClassPath = $componentClassDir . "/{$className}.php";

        if (!is_dir($componentClassDir)) {
            mkdir($componentClassDir, 0755, true);
        }

        if (file_exists($componentClassPath)) {
            echo "⚠ La classe Component\\{$className} existe déjà dans $selectedType.\n";
            exit(1);
        }

        $classTemplate = <<<PHP
<?php

namespace App\Interface\$selectedType\Component;

use App\Infrastructure\Blade\Blade;

class {$className}
{
    public function __construct(array \$data = [])
    {
        // Initialiser les propriétés du composant
    }

    public function render(): string
    {
        \$views = INTERFACE_PATH . "/$selectedType/View";
        \$cache = CACHE_PATH;
        \$blade = new Blade(\$views, \$cache);
        
        return \$blade->render('components.{$componentName}', [
            // Passer les données au template
        ]);
    }
}

PHP;
        file_put_contents($componentClassPath, $classTemplate);
        echo "✓ Classe Component\\{$className} créée dans Interface/$selectedType/Component/\n";
        exit(0);
    }
}
