<?php

declare(strict_types=1);

namespace App\Application\Service\Command;

use App\Application\Service\Command\Interface\CommandInterface;

use App\Application\Service\Command\Interface\OutputInterface;

class SeedCommand implements CommandInterface
{
    private OutputInterface $output;
    private array $seederOrder = [
        'UserSeeder.php',
        'PostSeeder.php',
    ];

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function execute(?string $name, array $options = []): void
    {
        $this->output->writeln("Exécution des seeders...\n");

        try {
            $seedersDir = MIGRATIONS_PATH . '/seeders';

            if (isset($options['class'])) {
                $seederFile = $seedersDir . '/' . $options['class'] . '.php';
                if (!file_exists($seederFile)) {
                    $this->output->writeln("Seeder " . $options['class'] . " introuvable.");
                    return;
                }
                $this->output->writeln("Seeder: " . $options['class']);
                $seeder = require $seederFile;
                if (is_callable($seeder)) {
                    $seeder();
                }
            } else {
                $seederFiles = glob($seedersDir . '/*.php');
                if (empty($seederFiles)) {
                    $this->output->writeln("Aucun seeder à exécuter.");
                    return;
                }

                // Trier selon l'ordre défini
                $orderedFiles = $this->orderSeeders($seederFiles);

                foreach ($orderedFiles as $file) {
                    $this->output->writeln("Seeder: " . basename($file));
                    $seeder = require $file;
                    if (is_callable($seeder)) {
                        $seeder();
                    }
                }
            }

            $this->output->writeln("\n✓ Seeding terminé avec succès.");
        } catch (\Exception $e) {
            $this->output->writeln("Erreur lors du seeding : " . $e->getMessage());
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
