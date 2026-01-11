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
            if (!$name) {
                echo "Usage: minor make:controller NomDuController\n";
                exit(1);
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
            $choiceIndex = (int)$choice - 1;
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

            $namespace = "App\\Interface\\$selectedType\\Controller";
            $template = <<<PHP
    <?php
    declare(strict_types=1);

    namespace $namespace;

    use App\Interface\Common\Attribute\Route;
    use App\Interface\Common\BaseController;

    class {$className} extends BaseController
    {
    
    }

    PHP;

            file_put_contents($filePath, $template);
            echo "Controller $className créé dans Interface/$selectedType/Controller.\n";
            exit(0);
        }
    }
}
