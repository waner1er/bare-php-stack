<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware;

use App\Infrastructure\Auth\Auth;
use App\Infrastructure\Session\Session;

class AuthMiddleware
{
    /**
     * Vérifie si l'utilisateur est authentifié
     * Redirige vers la page de login si non connecté
     */
    public static function handle(string $redirectTo = '/login'): void
    {
        if (!Auth::check()) {
            // Stocker l'URL actuelle pour rediriger après connexion
            Session::set('intended_url', $_SERVER['REQUEST_URI'] ?? '/');

            header('Location: ' . $redirectTo);
            exit;
        }
    }

    /**
     * Vérifie si l'utilisateur est un invité (non connecté)
     * Redirige vers une page si déjà connecté (utile pour login/register)
     */
    public static function guest(string $redirectTo = '/'): void
    {
        if (Auth::check()) {
            header('Location: ' . $redirectTo);
            exit;
        }
    }

    /**
     * Retourne true si l'utilisateur est authentifié (sans bloquer)
     */
    public static function check(): bool
    {
        return Auth::check();
    }

    /**
     * Récupère l'URL vers laquelle rediriger après connexion
     */
    public static function getIntendedUrl(string $default = '/'): string
    {
        $url = Session::get('intended_url', $default);
        Session::remove('intended_url');
        return $url;
    }
}
