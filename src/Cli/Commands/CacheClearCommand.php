<?php

namespace App\Cli\Commands;

use App\Cli\CommandInterface;

class CacheClearCommand implements CommandInterface
{
    public function execute(?string $name, array $options = []): void
    {
        $cacheDir = __DIR__ . '/../../../storage/cache';

        if (!is_dir($cacheDir)) {
            echo "Le dossier de cache n'existe pas.\n";
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

        echo "✓ Cache vidé : {$count} fichier(s) supprimé(s).\n";
    }
}
