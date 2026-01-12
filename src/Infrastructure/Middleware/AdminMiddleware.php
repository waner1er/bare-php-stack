<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware;

use App\Infrastructure\Auth\Auth;
use App\Infrastructure\Session\Session;

class AdminMiddleware
{
    public static function handle(string $loginRedirect = '/login', string $accessDeniedRedirect = '/'): void
    {
        if (!Auth::check()) {
            Session::set('intended_url', $_SERVER['REQUEST_URI'] ?? '/');

            header('Location: ' . $loginRedirect);
            exit;
        }

        $user = Auth::user();
        if (!$user || $user->getRole() !== 'admin') {
            Session::flash('error', 'Accès refusé. Vous devez être administrateur.');
            header('Location: ' . $accessDeniedRedirect);
            exit;
        }
    }


    public static function isAdmin(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        return $user && $user->getRole() === 'admin';
    }
}
