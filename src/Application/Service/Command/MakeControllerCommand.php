<?php

declare(strict_types=1);

namespace App\Application\Service\Command;

use App\Application\Service\Command\Interface\CommandInterface;

class MakeControllerCommand implements CommandInterface
{
    public function execute(?string $name, array $options = []): void
    {
        // Demande du nom du contrôleur si non fourni
        if (empty($name)) {
            echo "Entrez le nom du contrôleur (préfixe avant 'Controller') : ";
            $name = trim(fgets(STDIN));
            if (empty($name)) {
                echo "Le nom du contrôleur ne peut pas être vide.\n";
                exit(1);
            }
        }

        // Lecture dynamique des dossiers d'interface
        $interfacePath = defined('INTERFACE_PATH') ? INTERFACE_PATH : __DIR__ . '/../../../Interface';
        $dirs = array_filter(scandir($interfacePath), function ($d) use ($interfacePath) {
            return $d[0] !== '.' && is_dir($interfacePath . '/' . $d) && strtolower($d) !== 'common';
        });

        if (empty($dirs)) {
            echo "Aucune interface disponible dans $interfacePath.\n";
            exit(1);
        }

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
        $className = ucfirst($name) . 'Controller';

        $controllerDir = $interfacePath . "/$selectedType/Controller";
        if (!is_dir($controllerDir)) {
            mkdir($controllerDir, 0755, true);
        }

        $filePath = $controllerDir . "/{$className}.php";

        if (file_exists($filePath)) {
            echo "Le controller $className existe déjà dans $selectedType.\n";
            exit(1);
        }

        // Demander si le controller doit utiliser un repository
        echo "\nVoulez-vous utiliser un repository ? (o/n) : ";
        $useRepository = strtolower(trim(fgets(STDIN)));

        $repositoryCode = "";
        $repositoryImports = "";
        $constructorCode = "";
        $methodsCode = "";

        if ($useRepository === 'o' || $useRepository === 'oui' || $useRepository === 'y' || $useRepository === 'yes') {
            // Lister les modèles disponibles
            $entityPath = DOMAIN_PATH . "/Entity";
            $entities = [];

            if (is_dir($entityPath)) {
                $files = scandir($entityPath);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..' && str_ends_with($file, '.php')) {
                        $entities[] = str_replace('.php', '', $file);
                    }
                }
            }

            if (empty($entities)) {
                echo "⚠ Aucun modèle trouvé dans src/Domain/Entity.\n";
                echo "Le controller sera créé sans repository.\n";
            } else {
                echo "\nChoisissez le modèle à utiliser :\n";
                foreach ($entities as $i => $entity) {
                    $num = $i + 1;
                    echo "  [$num] $entity\n";
                }
                echo "  [0] Aucun (créer un controller vide)\n";
                echo "Votre choix : ";
                $entityChoice = trim(fgets(STDIN));
                $entityIndex = (int) $entityChoice - 1;

                if ($entityChoice !== '0' && is_numeric($entityChoice) && isset($entities[$entityIndex])) {
                    $modelName = $entities[$entityIndex];
                    $repositoryInterface = $modelName . 'RepositoryInterface';
                    $repositoryClass = $modelName . 'Repository';
                    $repositoryVar = lcfirst($modelName) . 'Repository';
                    $modelVar = lcfirst($modelName);
                    $routePrefix = strtolower($modelName) . 's';

                    $repositoryImports = "\nuse App\Domain\Repository\\{$repositoryInterface};\nuse App\Infrastructure\Repository\\{$repositoryClass};\nuse App\Domain\Entity\\{$modelName};";

                    // Syntaxe PHP 8.4 avec promoted properties et initialisation directe
                    $constructorCode = "\n    public function __construct(private {$repositoryInterface} \${$repositoryVar} = new {$repositoryClass}()) {}\n";

                    $methodsCode = <<<PHP

    #[Route('/{$routePrefix}', 'GET', '{$routePrefix}.index')]
    public function index(): void
    {
        \${$routePrefix} = \$this->{$repositoryVar}->findAll();
        \$this->render('{$routePrefix}.index', ['{$routePrefix}' => \${$routePrefix}]);
    }

    #[Route('/{$routePrefix}/{id}', 'GET', '{$routePrefix}.show')]
    public function show(int \$id): void
    {
        \${$modelVar} = \$this->{$repositoryVar}->find(\$id);

        if (!\${$modelVar}) {
            http_response_code(404);
            echo "{$modelName} non trouvé.";
            return;
        }

        \$this->render('{$routePrefix}.show', ['{$modelVar}' => \${$modelVar}]);
    }
PHP;
                }
            }
        }

        $namespace = "App\\Interface\\$selectedType\\Controller";
        $template = <<<PHP
<?php

declare(strict_types=1);

namespace $namespace;

use App\Interface\Common\Attribute\Route;
use App\Interface\Common\BaseController;{$repositoryImports}

class {$className} extends BaseController
{{$constructorCode}{$methodsCode}
}

PHP;

        file_put_contents($filePath, $template);
        echo "✓ Controller {$className} créé dans Interface/{$selectedType}/Controller.\n";
        exit(0);
    }
}
