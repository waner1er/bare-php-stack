<?php

namespace App\Cli\Commands;

use App\Cli\CommandInterface;

class SeedCommand implements CommandInterface
{
    // Définir l'ordre d'exécution des seeders
    private array $seederOrder = [
        'UserSeeder.php',
        'PostSeeder.php',
    ];

    public function execute(?string $name, array $options = []): void
    {
        echo "Exécution des seeders...\n\n";

        try {
            $seedersDir = __DIR__ . '/../../../migrations/seeders';

            if (isset($options['class'])) {
                $seederFile = $seedersDir . '/' . $options['class'] . '.php';
                if (!file_exists($seederFile)) {
                    echo "Seeder " . $options['class'] . " introuvable.\n";
                    return;
                }
                echo "Seeder: " . $options['class'] . "\n";
                $seeder = require $seederFile;
                if (is_callable($seeder)) {
                    $seeder();
                }
            } else {
                $seederFiles = glob($seedersDir . '/*.php');
                if (empty($seederFiles)) {
                    echo "Aucun seeder à exécuter.\n";
                    return;
                }

                // Trier selon l'ordre défini
                $orderedFiles = $this->orderSeeders($seederFiles);

                foreach ($orderedFiles as $file) {
                    echo "Seeder: " . basename($file) . "\n";
                    $seeder = require $file;
                    if (is_callable($seeder)) {
                        $seeder();
                    }
                }
            }

            echo "\n✓ Seeding terminé avec succès.\n";
        } catch (\Exception $e) {
            echo "Erreur lors du seeding : " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    private function orderSeeders(array $seederFiles): array
    {
        $ordered = [];
        $remaining = [];

        // Créer un mapping nom => chemin complet
        $fileMap = [];
        foreach ($seederFiles as $file) {
            $fileMap[basename($file)] = $file;
        }

        // Ajouter dans l'ordre défini
        foreach ($this->seederOrder as $seederName) {
            if (isset($fileMap[$seederName])) {
                $ordered[] = $fileMap[$seederName];
                unset($fileMap[$seederName]);
            }
        }

        // Ajouter les seeders non listés à la fin
        foreach ($fileMap as $file) {
            $remaining[] = $file;
        }

        return array_merge($ordered, $remaining);
    }
}
