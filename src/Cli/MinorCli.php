<?php

namespace App\Cli;

use App\Cli\Commands\MakeControllerCommand;
use App\Cli\Commands\MakeModelCommand;
use App\Cli\Commands\MakeComponentCommand;
use App\Cli\Commands\MakeSeederCommand;
use App\Cli\Commands\DatabaseCreateCommand;
use App\Cli\Commands\DatabaseDropCommand;
use App\Cli\Commands\MigrateCommand;
use App\Cli\Commands\SeedCommand;
use App\Cli\Commands\CacheClearCommand;
use App\Cli\Commands\SessionCleanCommand;

class MinorCli
{
    private array $commands = [];

    public function __construct()
    {
        $this->commands = [
            'make:model' => new MakeModelCommand(),
            'make:controller' => new MakeControllerCommand(),
            'make:component' => new MakeComponentCommand(),
            'make:seeder' => new MakeSeederCommand(),
            'db:create' => new DatabaseCreateCommand(),
            'db:drop' => new DatabaseDropCommand(),
            'migrate' => new MigrateCommand(),
            'db:seed' => new SeedCommand(),
            'cache:clear' => new CacheClearCommand(),
            'session:clean' => new SessionCleanCommand(),
        ];
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
        echo "Minor CLI - Générateur de code\n\n";
        echo "Commandes disponibles :\n";
        echo "  make:controller <nom>       Créer un nouveau controller\n";
        echo "  make:model <nom> [--migration]  Créer un nouveau modèle\n";
        echo "  make:component <nom> [--class]  Créer un nouveau composant Blade\n";
        echo "  make:seeder <nom>           Créer un nouveau seeder\n";
        echo "  db:create                   Créer la base de données\n";
        echo "  db:drop                     Supprimer la base de données\n";
        echo "  migrate                     Exécuter les migrations\n";
        echo "  db:seed                     Remplir la base avec des données de test\n";
        echo "  cache:clear                 Vider le cache Blade\n";
        echo "  session:clean               Nettoyer les sessions expirées\n";
    }
}
