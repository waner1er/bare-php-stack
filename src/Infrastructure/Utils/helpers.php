<?php

use App\Infrastructure\Router\Router;
use App\Infrastructure\Middleware\CsrfMiddleware;
use App\Infrastructure\Auth\Auth;

if (!function_exists('route')) {
    function route(string $name, array $params = []): string
    {
        $router = Router::getInstance();
        return $router ? $router->url($name, $params) : '/';
    }
}

if (!function_exists('csrf_field')) {

    function csrf_field(): string
    {
        return CsrfMiddleware::field();
    }
}

if (!function_exists('csrf_token')) {

    function csrf_token(): string
    {
        return CsrfMiddleware::getToken();
    }
}

if (!function_exists('auth')) {

    function auth(): ?array
    {
        return Auth::user();
    }
}

if (!function_exists('guest')) {

    function guest(): bool
    {
        return !Auth::check();
    }
}
