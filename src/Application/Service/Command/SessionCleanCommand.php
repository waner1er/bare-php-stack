<?php

namespace App\Application\Service\Command;

use App\Infrastructure\Database\Database;
use App\Infrastructure\Session\DatabaseSessionHandler;
use App\Application\Service\Command\CommandInterface;

class SessionCleanCommand implements CommandInterface
{
    public function getName(): string
    {
        return 'session:clean';
    }

    public function getDescription(): string
    {
        return 'Nettoie les sessions expirées de la base de données';
    }

    public function execute(?string $name): void
    {
        $pdo = Database::getConnection();
        $lifetime = (int) ($_ENV['SESSION_LIFETIME'] ?? 7200);
        $handler = new DatabaseSessionHandler($pdo, $lifetime);

        $count = $handler->clean();

        if ($count > 0) {
            echo "✓ {$count} session(s) expirée(s) supprimée(s)\n";
        } else {
            echo "✓ Aucune session expirée à nettoyer\n";
        }
    }
}
