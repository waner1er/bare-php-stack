<?php

declare(strict_types=1);

namespace App\Infrastructure\Router;

use App\Interface\Common\Attribute\Route;
use ReflectionClass;
use ReflectionMethod;


class Router
{
    private array $routes = [];
    private static ?Router $instance = null;

    public static function getInstance(): ?Router
    {
        return self::$instance;
    }

    public function __construct()
    {
        self::$instance = $this;
    }

    public function registerController(string $controllerClass): void
    {
        $refClass = new ReflectionClass($controllerClass);
        foreach ($refClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $attributes = $method->getAttributes(Route::class);
            foreach ($attributes as $attribute) {
                /** @var Route $route */
                $route = $attribute->newInstance();
                $routeName = $route->name ?? strtolower($controllerClass) . '.' . $method->getName();
                $this->routes[] = [
                    'path' => $route->path,
                    'method' => $route->method,
                    'controller' => $controllerClass,
                    'action' => $method->getName(),
                    'name' => $routeName,
                ];
            }
        }
    }

    public function dispatch(string $uri, string $httpMethod = 'GET'): void
    {
        foreach ($this->routes as $route) {
            $pattern = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $route['path']);
            $pattern = "#^{$pattern}$#";
            if (
                preg_match($pattern, $uri, $matches)
                && $httpMethod === $route['method']
            ) {
                $controller = new $route['controller']();
                $params = [];
                $refMethod = new \ReflectionMethod($controller, $route['action']);
                foreach ($refMethod->getParameters() as $param) {
                    $params[] = $matches[$param->getName()] ?? null;
                }
                $refMethod->invokeArgs($controller, $params);
                return;
            }
        }
        http_response_code(404);
        echo "Page not found";
    }

    public function url(string $name, array $params = []): string
    {
        foreach ($this->routes as $route) {
            if ($route['name'] === $name) {
                $path = $route['path'];
                foreach ($params as $key => $value) {
                    $path = str_replace('{' . $key . '}', $value, $path);
                }
                return $path;
            }
        }
        return '/';
    }
}
