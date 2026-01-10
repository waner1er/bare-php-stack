<?php

declare(strict_types=1);

namespace App\Application\Service\Command;

use App\Application\Service\Command\CommandInterface;
use App\Infrastructure\Database\Database;

class MigrateCommand implements CommandInterface
{
    public function execute(?string $name, array $options = []): void
    {
        try {
            $pdo = Database::getConnection();

            // Créer la table migrations si elle n'existe pas
            $this->ensureMigrationsTable($pdo);

            $migrationsDir = MIGRATIONS_PATH . '/files';
            $migrationFiles = glob($migrationsDir . '/*.php');

            if (empty($migrationFiles)) {
                echo "Aucune migration à exécuter.\n";
                return;
            }

            // Récupérer les migrations déjà exécutées
            $executedMigrations = $this->getExecutedMigrations($pdo);

            // Collecter les nouvelles migrations
            $newMigrations = [];
            foreach ($migrationFiles as $file) {
                $fileName = basename($file);

                // Ignorer si déjà exécutée
                if (in_array($fileName, $executedMigrations, true)) {
                    continue;
                }

                $sql = require $file;
                if (is_string($sql) && !empty($sql)) {
                    $newMigrations[] = [
                        'file' => $fileName,
                        'sql' => $sql
                    ];
                }
            }

            if (empty($newMigrations)) {
                echo "Aucune nouvelle migration à exécuter.\n";
                echo "Toutes les migrations sont à jour.\n";
                return;
            }

            // Exécuter les nouvelles migrations
            $executed = 0;
            foreach ($newMigrations as $migration) {
                echo "Migration: {$migration['file']}... ";
                $pdo->exec($migration['sql']);

                // Enregistrer la migration comme exécutée
                $this->recordMigration($pdo, $migration['file']);

                echo "✓\n";
                $executed++;
            }

            echo "\nMigration terminée. {$executed} nouvelle(s) migration(s) exécutée(s).\n";
        } catch (\Exception $e) {
            echo "Erreur lors de la migration : " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    private function ensureMigrationsTable(\PDO $pdo): void
    {
        $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL UNIQUE,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    private function getExecutedMigrations(\PDO $pdo): array
    {
        $stmt = $pdo->query("SELECT migration FROM migrations");
        return $stmt ? $stmt->fetchAll(\PDO::FETCH_COLUMN) : [];
    }

    private function recordMigration(\PDO $pdo, string $migrationName): void
    {
        $stmt = $pdo->prepare("INSERT INTO migrations (migration) VALUES (?)");
        $stmt->execute([$migrationName]);
    }
}
