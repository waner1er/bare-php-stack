<?php

use App\Router\Router;
use App\Middleware\CsrfMiddleware;
use App\Tools\Auth;

if (!function_exists('route')) {
    function route(string $name, array $params = []): string
    {
        $router = Router::getInstance();
        return $router ? $router->url($name, $params) : '/';
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Génère un champ input hidden avec le token CSRF
     */
    function csrf_field(): string
    {
        return CsrfMiddleware::field();
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Récupère le token CSRF actuel
     */
    function csrf_token(): string
    {
        return CsrfMiddleware::getToken();
    }
}

if (!function_exists('auth')) {
    /**
     * Récupère l'utilisateur connecté ou null
     */
    function auth(): ?array
    {
        return Auth::user();
    }
}

if (!function_exists('guest')) {
    /**
     * Vérifie si l'utilisateur est un invité (non connecté)
     */
    function guest(): bool
    {
        return !Auth::check();
    }
}
