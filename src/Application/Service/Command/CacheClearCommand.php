<?php

declare(strict_types=1);

namespace App\Application\Service\Command;

use App\Application\Service\Command\Interface\CommandInterface;
use App\Application\Service\Command\Interface\OutputInterface;

class CacheClearCommand implements CommandInterface
{
    private OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function execute(?string $name, array $options = []): void
    {
        $cacheDir = CACHE_PATH;

        if (!is_dir($cacheDir)) {
            $this->output->writeln("Le dossier de cache n'existe pas.");
            return;
        }

        $files = glob($cacheDir . '/*.php');
        $count = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $count++;
            }
        }

        $this->output->writeln("✓ Cache vidé : {$count} fichier(s) supprimé(s).");
    }
}
