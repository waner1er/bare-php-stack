<?php

declare(strict_types=1);

namespace App\Interface\Cli;

use App\Interface\Cli\SeedCommand;
use App\Interface\Cli\MigrateCommand;
use App\Interface\Cli\MakeModelCommand;
use App\Interface\Cli\CacheClearCommand;
use App\Interface\Cli\MakeSeederCommand;
use App\Interface\Cli\DatabaseDropCommand;
use App\Interface\Cli\SessionCleanCommand;
use App\Interface\Cli\MakeComponentCommand;
use App\Interface\Cli\DatabaseCreateCommand;
use App\Interface\Cli\MakeControllerCommand;
use App\Interface\Cli\MakeCrudCommand;

class MinorCli
{
    private array $commands = [];
    private \App\Interface\Cli\Contract\OutputInterface $output;
    private array $env = [];

    public function __construct(?\App\Interface\Cli\Contract\OutputInterface $output = null)
    {
        $this->output = $output ?? new ConsoleOutput();
        $this->env = $this->loadEnv();
        $drop    = new DatabaseDropCommand($this->output, $this->env);
        $create  = new DatabaseCreateCommand($this->output, $this->env);
        $migrate = new MigrateCommand($this->output);
        $seed    = new SeedCommand($this->output);

        $this->commands = [
            'make:model'      => new MakeModelCommand($this->output),
            'make:controller' => new MakeControllerCommand($this->output),
            'make:component'  => new MakeComponentCommand($this->output),
            'make:seeder'     => new MakeSeederCommand($this->output),
            'make:crud'       => new MakeCrudCommand($this->output),
            'db:create'       => $create,
            'db:drop'         => $drop,
            'migrate'         => $migrate,
            'migrate:fresh'   => new MigrateFreshCommand($this->output, $drop, $create, $migrate, $seed),
            'db:seed'         => $seed,
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
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $env[trim($key)] = trim($value);
        }
        return $env;
    }

    public function run(array $argv): void
    {
        $command = $argv[1] ?? null;
        $rest = array_slice($argv, 2);

        $options = array_values(array_filter($rest, fn($a) => str_starts_with($a, '--')));
        $params  = array_values(array_filter($rest, fn($a) => !str_starts_with($a, '--')));
        $param   = $params[0] ?? null;

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
        $this->output->writeln("  db:drop [--force]           Supprimer la base de données");
        $this->output->writeln("  migrate                     Exécuter les migrations");
        $this->output->writeln("  migrate:fresh [--seed]      Drop + create + migrate (et seed si --seed)");
        $this->output->writeln("  db:seed                     Remplir la base avec des données de test");
        $this->output->writeln("  cache:clear                 Vider le cache Blade");
        $this->output->writeln("  session:clean               Nettoyer les sessions expirées");
    }
}
