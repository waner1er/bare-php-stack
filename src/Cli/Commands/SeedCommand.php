<?php

namespace App\Cli\Commands;

use App\Cli\CommandInterface;

class SeedCommand implements CommandInterface
{
    public function execute(?string $name, array $options = []): void
    {
        echo "Exécution des seeders...\n\n";

        try {
            $seedersDir = __DIR__ . '/../../../migrations/seeders';
            $seederFiles = glob($seedersDir . '/*.php');

            if (empty($seederFiles)) {
                echo "Aucun seeder à exécuter.\n";
                return;
            }

            // Exécuter chaque seeder
            foreach ($seederFiles as $file) {
                echo "Seeder: " . basename($file) . "\n";
                $seeder = require $file;

                if (is_callable($seeder)) {
                    $seeder();
                }
            }

            echo "\n✓ Seeding terminé avec succès.\n";
        } catch (\Exception $e) {
            echo "Erreur lors du seeding : " . $e->getMessage() . "\n";
            exit(1);
        }
    }
}
