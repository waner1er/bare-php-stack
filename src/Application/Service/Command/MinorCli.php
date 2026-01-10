<?php

declare(strict_types=1);

namespace App\Application\Service\Command;

use App\Application\Service\Command\SeedCommand;
use App\Application\Service\Command\MigrateCommand;
use App\Application\Service\Command\MakeModelCommand;
use App\Application\Service\Command\CacheClearCommand;
use App\Application\Service\Command\MakeSeederCommand;
use App\Application\Service\Command\DatabaseDropCommand;
use App\Application\Service\Command\SessionCleanCommand;
use App\Application\Service\Command\MakeComponentCommand;
use App\Application\Service\Command\DatabaseCreateCommand;
use App\Application\Service\Command\MakeControllerCommand;




class MinorCli
{
    private array $commands = [];
    private \App\Application\Service\Command\Interface\OutputInterface $output;
    private array $env = [];

    public function __construct(?\App\Application\Service\Command\Interface\OutputInterface $output = null)
    {
        $this->output = $output ?? new ConsoleOutput();
        $this->env = $this->loadEnv();
        $this->commands = [
            'make:model'      => new MakeModelCommand($this->output),
            'make:controller' => new MakeControllerCommand($this->output),
            'make:component'  => new MakeComponentCommand($this->output),
            'make:seeder'     => new MakeSeederCommand($this->output),
            'db:create'       => new DatabaseCreateCommand($this->output, $this->env),
            'db:drop'         => new DatabaseDropCommand($this->output, $this->env),
            'migrate'         => new MigrateCommand($this->output),
            'db:seed'         => new SeedCommand($this->output),
            'cache:clear'     => new CacheClearCommand($this->output),
            'session:clean'   => new SessionCleanCommand($this->output),
        ];
    }

    private function loadEnv(): array
    {
        $envPath = ENV_FILE;
        if (!file_exists($envPath)) {
            throw new \RuntimeException('.env file not found');
        }
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $env = [];
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            [$key, $value] = explode('=', $line, 2);
            $env[trim($key)] = trim($value);
        }
        return $env;
    }

    public function run(array $argv): void
    {
        $command = $argv[1] ?? null;
        $param = $argv[2] ?? null;
        $options = array_slice($argv, 3);

        if (isset($this->commands[$command])) {
            $this->commands[$command]->execute($param, $options);
        } else {
            $this->showHelp();
        }
    }

    private function showHelp(): void
    {
        $this->output->writeln("Minor CLI - Générateur de code\n");
        $this->output->writeln("Commandes disponibles :");
        $this->output->writeln("  make:controller <nom>       Créer un nouveau controller");
        $this->output->writeln("  make:model <nom> [--migration]  Créer un nouveau modèle");
        $this->output->writeln("  make:component <nom> [--class]  Créer un nouveau composant Blade");
        $this->output->writeln("  make:seeder <nom>           Créer un nouveau seeder");
        $this->output->writeln("  db:create                   Créer la base de données");
        $this->output->writeln("  db:drop                     Supprimer la base de données");
        $this->output->writeln("  migrate                     Exécuter les migrations");
        $this->output->writeln("  db:seed                     Remplir la base avec des données de test");
        $this->output->writeln("  cache:clear                 Vider le cache Blade");
        $this->output->writeln("  session:clean               Nettoyer les sessions expirées");
    }
}
