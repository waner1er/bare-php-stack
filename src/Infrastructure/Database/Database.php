<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

class Database
{
    private static ?\PDO $pdo = null;

    public static function getConnection(): \PDO
    {
        if (self::$pdo === null) {
            $env = self::loadEnv();
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                $env['DB_HOST'] ?? 'localhost',
                $env['DB_NAME'] ?? '',
            );
            self::$pdo = new \PDO(
                $dsn,
                $env['DB_USER'] ?? '',
                $env['DB_PASS'] ?? '',
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                ],
            );
        }
        return self::$pdo;
    }

    private static function loadEnv(): array
    {
        $envPath = __DIR__ . '/../../../.env';
        if (!file_exists($envPath)) {
            throw new \RuntimeException('.env file not found');
        }
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $env = [];
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $env[trim($key)] = trim($value);
        }
        return $env;
    }
}
