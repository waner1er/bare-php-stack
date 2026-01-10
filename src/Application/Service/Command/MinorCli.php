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
