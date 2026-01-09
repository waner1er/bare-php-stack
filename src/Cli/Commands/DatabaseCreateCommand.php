<?php

namespace App\Cli\Commands;

use App\Cli\CommandInterface;

class DatabaseCreateCommand implements CommandInterface
{
    public function execute(?string $name, array $options = []): void
    {
        try {
            $env = $this->loadEnv();

            $host = $env['DB_HOST'] ?? 'localhost';
            $dbName = $env['DB_NAME'] ?? '';
            $user = $env['DB_USER'] ?? '';
            $pass = $env['DB_PASS'] ?? '';

            if (empty($dbName)) {
                echo "Erreur: DB_NAME n'est pas défini dans .env\n";
                exit(1);
            }

            $dsn = "mysql:host={$host};charset=utf8mb4";
            $pdo = new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);

            $stmt = $pdo->query("SHOW DATABASES LIKE '{$dbName}'");
            $exists = $stmt->fetch();

            if ($exists) {
                echo "La base de données '{$dbName}' existe déjà.\n";
                return;
            }

            $sql = "CREATE DATABASE `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            $pdo->exec($sql);

            echo "Base de données '{$dbName}' créée avec succès.\n";
        } catch (\PDOException $e) {
            echo "Erreur lors de la création de la base de données : " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    private function loadEnv(): array
    {
        $envPath = __DIR__ . '/../../../.env';
        if (!file_exists($envPath)) {
            throw new \RuntimeException('.env file not found');
        }
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $env = [];
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            [$key, $value] = explode('=', $line, 2);
            $env[trim($key)] = trim($value);
        }
        return $env;
    }
}
