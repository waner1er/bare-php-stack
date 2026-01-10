<?php

declare(strict_types=1);

namespace App\Application\Service\Command;

use App\Application\Service\Command\Interface\OutputInterface;
use App\Application\Service\Command\Interface\CommandInterface;

class DatabaseDropCommand implements CommandInterface
{
    private OutputInterface $output;
    private array $env;

    public function __construct(OutputInterface $output, array $env)
    {
        $this->output = $output;
        $this->env = $env;
    }

    public function execute(?string $name, array $options = []): void
    {
        try {
            $host = $this->env['DB_HOST'] ?? 'localhost';
            $dbName = $this->env['DB_NAME'] ?? '';
            $user = $this->env['DB_USER'] ?? '';
            $pass = $this->env['DB_PASS'] ?? '';

            if (empty($dbName)) {
                $this->output->writeln("Erreur: DB_NAME n'est pas défini dans .env");
                return;
            }

            $dsn = "mysql:host={$host};charset=utf8mb4";
            $pdo = new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);

            $stmt = $pdo->query("SHOW DATABASES LIKE '{$dbName}'");
            $exists = $stmt->fetch();

            if (!$exists) {
                $this->output->writeln("La base de données '{$dbName}' n'existe pas.");
                return;
            }

            $this->output->writeln("Êtes-vous sûr de vouloir supprimer la base de données '{$dbName}' ? (o/n): ");
            $confirmation = trim(fgets(STDIN));

            if (strtolower($confirmation) !== 'o') {
                $this->output->writeln("Suppression annulée.");
                return;
            }

            $sql = "DROP DATABASE `{$dbName}`";
            $pdo->exec($sql);

            $this->output->writeln("Base de données '{$dbName}' supprimée avec succès.");
        } catch (\PDOException $e) {
            $this->output->writeln("Erreur lors de la suppression de la base de données : " . $e->getMessage());
        }
    }
}
