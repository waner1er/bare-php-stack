<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware;

use App\Infrastructure\Session\Session;

class CsrfMiddleware
{
    private static string $sessionKey = '_csrf_token';
    private static string $formField = '_token';

    public static function generateToken(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::set(self::$sessionKey, $token);
        return $token;
    }

    public static function getToken(): string
    {
        $token = Session::get(self::$sessionKey);
        if (!$token) {
            $token = self::generateToken();
        }
        return $token;
    }

    public static function validateToken(?string $token): bool
    {
        $sessionToken = Session::get(self::$sessionKey);
        if (!$sessionToken || !$token) {
            return false;
        }
        return hash_equals($sessionToken, $token);
    }

    public static function field(): string
    {
        $token = self::getToken();
        return '<input type="hidden" name="' . self::$formField . '" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function handle(): void
    {
        $methodsToProtect = ['POST', 'PUT', 'DELETE', 'PATCH'];
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if (in_array($method, $methodsToProtect, true)) {
            $token = $_POST[self::$formField]
                ?? $_SERVER['HTTP_X_CSRF_TOKEN']
                ?? null;

            if (!self::validateToken($token)) {
                http_response_code(403);
                exit('Forbidden: Invalid CSRF token.');
            }

            self::generateToken();
        }
    }

    public static function check(): bool
    {
        $token = $_POST[self::$formField]
            ?? $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? null;

        return self::validateToken($token);
    }
}
