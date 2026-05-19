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

    public function addPath(string $path): void
    {
        if (is_dir($path)) {
            $this->controllerPaths[] = $path;
        }
    }

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

    private function loadController(string $filePath): void
    {
        $className = $this->extractClassName($filePath);

        if ($className && $this->isController($className)) {
            error_log("ControllerLoader: Enregistrement du controller {$className}");
            $this->router->registerController($className);
        }
    }

    private function extractClassName(string $filePath): ?string
    {
        $content = file_get_contents($filePath);

        if (preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches)) {
            $namespace = $namespaceMatches[1];
        } else {
            return null;
        }

        if (preg_match('/class\s+(\w+)/', $content, $classMatches)) {
            $className = $classMatches[1];
        } else {
            return null;
        }

        $fullClassName = $namespace . '\\' . $className;

        if (class_exists($fullClassName)) {
            return $fullClassName;
        }

        return null;
    }

    private function isController(string $className): bool
    {
        try {
            $reflection = new ReflectionClass($className);

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
