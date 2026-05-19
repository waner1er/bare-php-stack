<?php

namespace App\Interface\Cli;

use App\Infrastructure\Database\Database;
use App\Infrastructure\Session\DatabaseSessionHandler;
use App\Interface\Cli\Contract\CommandInterface;
use App\Interface\Cli\Contract\OutputInterface;

class SessionCleanCommand implements CommandInterface
{
    private OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function getName(): string
    {
        return 'session:clean';
    }

    public function getDescription(): string
    {
        return 'Nettoie les sessions expirées de la base de données';
    }

    public function execute(?string $name, array $options = []): void
    {
        $pdo = Database::getConnection();
        $lifetime = (int) ($_ENV['SESSION_LIFETIME'] ?? 7200);
        $handler = new DatabaseSessionHandler($pdo, $lifetime);

        $count = $handler->clean();

        if ($count > 0) {
            $this->output->writeln("✓ {$count} session(s) expirée(s) supprimée(s)");
        } else {
            $this->output->writeln("✓ Aucune session expirée à nettoyer");
        }
    }
}
