<?php

declare(strict_types=1);

namespace App\Application\Service\Command;

use App\Application\Service\Command\CommandInterface;

class MakeControllerCommand implements CommandInterface
{
    public function execute(?string $name): void
    {
        if (!$name) {
            echo "Usage: minor make:controller NomDuController\n";
            exit(1);
        }

        $className = ucfirst($name) . 'Controller';
        $filePath = CONTROLLER_PATH . "/{$className}.php";

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
