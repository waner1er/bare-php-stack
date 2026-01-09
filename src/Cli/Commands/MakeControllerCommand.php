<?php

namespace App\Cli\Commands;

use App\Cli\CommandInterface;

class MakeControllerCommand implements CommandInterface
{
    public function execute(?string $name): void
    {
        if (!$name) {
            echo "Usage: minor make:controller NomDuController\n";
            exit(1);
        }

        $className = ucfirst($name) . 'Controller';
        $filePath = __DIR__ . "/../../Controller/{$className}.php";

        if (file_exists($filePath)) {
            echo "Le controller $className existe déjà.\n";
            exit(1);
        }

        $template = <<<PHP
<?php

declare(strict_types=1);

namespace App\Controller;

use App\Attribute\Route;

class $className extends BaseController
{
}

PHP;

        file_put_contents($filePath, $template);
        echo "Controller $className créé dans src/Controller.\n";
    }
}
