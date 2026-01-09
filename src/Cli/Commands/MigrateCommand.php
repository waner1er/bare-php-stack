<?php

namespace App\Cli\Commands;

use App\Cli\CommandInterface;
use App\Tools\Database;

class MigrateCommand implements CommandInterface
{
    public function execute(?string $name, array $options = []): void
    {
        try {
            $pdo = Database::getConnection();

            $migrationsDir = __DIR__ . '/../../../migrations/files';
            $migrationFiles = glob($migrationsDir . '/*.php');

            if (empty($migrationFiles)) {
                echo "Aucune migration à exécuter.\n";
                return;
            }

            // Collecter toutes les requêtes SQL
            $sqlQueries = [];
            foreach ($migrationFiles as $file) {
                $sql = require $file;
                if (is_string($sql) && !empty($sql)) {
                    $sqlQueries[] = [
                        'file' => basename($file),
                        'sql' => $sql
                    ];
                }
            }

            // Exécuter toutes les requêtes
            foreach ($sqlQueries as $query) {
                echo "Migration: {$query['file']}... ";
                $pdo->exec($query['sql']);
                echo "✓\n";
            }

            echo "\nMigration terminée. " . count($sqlQueries) . " table(s) créée(s).\n";
        } catch (\Exception $e) {
            echo "Erreur lors de la migration : " . $e->getMessage() . "\n";
            exit(1);
        }
    }
}
