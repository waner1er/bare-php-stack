<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware;

use App\Infrastructure\Auth\Auth;
use App\Infrastructure\Session\Session;

class AdminMiddleware
{
    /**
     * Vérifie si l'utilisateur est authentifié ET a le rôle admin
     * Redirige vers la page de login si non connecté
     * Redirige vers la page d'accueil si non admin
     */
    public static function handle(string $loginRedirect = '/login', string $accessDeniedRedirect = '/'): void
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            // Stocker l'URL actuelle pour rediriger après connexion
            Session::set('intended_url', $_SERVER['REQUEST_URI'] ?? '/');

            header('Location: ' . $loginRedirect);
            exit;
        }

        // Vérifier si l'utilisateur a le rôle admin
        $user = Auth::user();
        if (!$user || $user->getRole() !== 'admin') {
            Session::flash('error', 'Accès refusé. Vous devez être administrateur.');
            header('Location: ' . $accessDeniedRedirect);
            exit;
        }
    }

    /**
     * Vérifie si l'utilisateur actuel est admin (sans bloquer)
     */
    public static function isAdmin(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        return $user && $user->getRole() === 'admin';
    }
}
