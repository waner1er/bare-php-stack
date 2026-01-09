<?php

use App\Router\Router;

if (!function_exists('route')) {
    function route(string $name, array $params = []): string
    {
        $router = Router::getInstance();
        return $router ? $router->url($name, $params) : '/';
    }
}
