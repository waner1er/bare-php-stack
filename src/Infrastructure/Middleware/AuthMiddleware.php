<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware;

use App\Infrastructure\Auth\Auth;
use App\Infrastructure\Session\Session;

class AuthMiddleware
{
    public static function handle(string $redirectTo = '/login'): void
    {
        if (!Auth::check()) {
            Session::set('intended_url', $_SERVER['REQUEST_URI'] ?? '/');

            header('Location: ' . $redirectTo);
            exit;
        }
    }


    public static function guest(string $redirectTo = '/'): void
    {
        if (Auth::check()) {
            header('Location: ' . $redirectTo);
            exit;
        }
    }

    public static function check(): bool
    {
        return Auth::check();
    }

    public static function getIntendedUrl(string $default = '/'): string
    {
        $url = Session::get('intended_url', $default);
        Session::remove('intended_url');
        return $url;
    }
}
