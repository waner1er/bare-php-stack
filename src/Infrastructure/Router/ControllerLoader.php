<?php

declare(strict_types=1);

namespace App\Infrastructure\Router;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

class ControllerLoader
{
    private Router $router;
    private array $controllerPaths = [];

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Ajoute un chemin où scanner les controllers
     */
    public function addPath(string $path): void
    {
        if (is_dir($path)) {
            $this->controllerPaths[] = $path;
        }
    }

    /**
     * Scanne et enregistre automatiquement tous les controllers
     */
    public function loadControllers(): void
    {
        if (empty($this->controllerPaths)) {
            error_log('ControllerLoader: Aucun chemin de controller défini');
            return;
        }

        foreach ($this->controllerPaths as $path) {
            error_log("ControllerLoader: Scan du dossier {$path}");
            $this->scanDirectory($path);
        }
    }

    /**
     * Scanne récursivement un dossier pour trouver les controllers
     */
    private function scanDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            error_log("ControllerLoader: Le dossier {$directory} n'existe pas");
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $this->loadController($file->getPathname());
            }
        }
    }

    /**
     * Charge un controller à partir d'un fichier
     */
    private function loadController(string $filePath): void
    {
        // Extraire le namespace et la classe depuis le fichier
        $className = $this->extractClassName($filePath);

        if ($className && $this->isController($className)) {
            error_log("ControllerLoader: Enregistrement du controller {$className}");
            $this->router->registerController($className);
        }
    }

    /**
     * Extrait le nom complet de la classe (avec namespace) depuis un fichier PHP
     */
    private function extractClassName(string $filePath): ?string
    {
        $content = file_get_contents($filePath);

        // Extraire le namespace
        if (preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches)) {
            $namespace = $namespaceMatches[1];
        } else {
            return null;
        }

        // Extraire le nom de la classe
        if (preg_match('/class\s+(\w+)/', $content, $classMatches)) {
            $className = $classMatches[1];
        } else {
            return null;
        }

        $fullClassName = $namespace . '\\' . $className;

        // Vérifier que la classe existe
        if (class_exists($fullClassName)) {
            return $fullClassName;
        }

        return null;
    }

    /**
     * Vérifie si une classe est un controller (contient des routes)
     */
    private function isController(string $className): bool
    {
        try {
            $reflection = new ReflectionClass($className);

            // Vérifier si au moins une méthode a l'attribut Route
            foreach ($reflection->getMethods() as $method) {
                if (!empty($method->getAttributes(\App\Interface\Common\Attribute\Route::class))) {
                    return true;
                }
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }
}
