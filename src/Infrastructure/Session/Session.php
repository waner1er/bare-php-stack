<?php

namespace App\Infrastructure\Session;

use App\Infrastructure\Database\Database;

class Session
{
    private static ?DatabaseSessionHandler $handler = null;

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            if (self::$handler === null) {
                $pdo = Database::getConnection();
                $lifetime = (int) ($_ENV['SESSION_LIFETIME'] ?? 7200);
                self::$handler = new DatabaseSessionHandler($pdo, $lifetime);
                session_set_save_handler(self::$handler, true);
            }

            $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

            session_start([
                'cookie_lifetime' => 0,
                'cookie_httponly' => true,
                'cookie_secure' => $isSecure,
                'cookie_samesite' => 'Lax',
                'gc_maxlifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 7200),
                'gc_probability' => 1,
                'gc_divisor' => 100,
            ]);
        }
    }

    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        self::start();
        session_destroy();
        $_SESSION = [];
    }

    public static function flash(string $key, mixed $value): void
    {
        self::set('_flash_' . $key, $value);
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        self::start();
        $value = $_SESSION['_flash_' . $key] ?? $default;
        unset($_SESSION['_flash_' . $key]);
        return $value;
    }
}
