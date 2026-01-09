<?php

namespace App\Cli\Commands;

use App\Cli\CommandInterface;

class DatabaseDropCommand implements CommandInterface
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

            // Vérifier si la base de données existe
            $stmt = $pdo->query("SHOW DATABASES LIKE '{$dbName}'");
            $exists = $stmt->fetch();

            if (!$exists) {
                echo "La base de données '{$dbName}' n'existe pas.\n";
                return;
            }

            // Demander confirmation
            echo "Êtes-vous sûr de vouloir supprimer la base de données '{$dbName}' ? (o/n): ";
            $confirmation = trim(fgets(STDIN));

            if (strtolower($confirmation) !== 'o') {
                echo "Suppression annulée.\n";
                return;
            }

            // Supprimer la base de données
            $sql = "DROP DATABASE `{$dbName}`";
            $pdo->exec($sql);

            echo "Base de données '{$dbName}' supprimée avec succès.\n";
        } catch (\PDOException $e) {
            echo "Erreur lors de la suppression de la base de données : " . $e->getMessage() . "\n";
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
