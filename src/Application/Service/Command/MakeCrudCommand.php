<?php

declare(strict_types=1);

namespace App\Application\Service\Command;

use App\Application\Service\Command\Interface\CommandInterface;
use App\Application\Service\Command\Interface\OutputInterface;

class MakeCrudCommand implements CommandInterface
{
    private OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function execute(?string $name, array $options = []): void
    {
        if (empty($name)) {
            $this->output->writeln("❌ Le nom de l'entité est requis.");
            $this->output->writeln("Usage: php minor make:crud <Entity>");
            return;
        }

        $entityName = ucfirst($name);
        $entityClass = "App\\Domain\\Entity\\{$entityName}";

        // Vérifier que l'entité existe
        if (!class_exists($entityClass)) {
            $this->output->writeln("❌ L'entité {$entityName} n'existe pas dans Domain/Entity.");
            return;
        }

        $crudClassName = "{$entityName}Resource";
        $crudFilePath = SRC_PATH . "/Interface/Admin/Crud/{$crudClassName}.php";

        if (file_exists($crudFilePath)) {
            $this->output->writeln("❌ Le CrudResource {$crudClassName} existe déjà.");
            return;
        }

        // Analyser l'entité pour détecter ses propriétés
        $reflection = new \ReflectionClass($entityClass);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

        $inputs = [];
        $columns = [];

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyType = $property->getType();

            // Ignorer l'ID dans le formulaire
            if ($propertyName === 'id') {
                $columns[] = $this->generateColumnCode($propertyName, $propertyType);
                continue;
            }

            $inputs[] = $this->generateInputCode($propertyName, $propertyType);
            $columns[] = $this->generateColumnCode($propertyName, $propertyType);
        }

        $inputsCode = implode("\n            ", $inputs);
        $columnsCode = implode("\n            ", $columns);

        $content = $this->generateCrudResourceContent(
            $entityName,
            $crudClassName,
            $inputsCode,
            $columnsCode
        );

        if (!is_dir(dirname($crudFilePath))) {
            mkdir(dirname($crudFilePath), 0755, true);
        }

        file_put_contents($crudFilePath, $content);

        $this->output->writeln("✓ CrudResource créé avec succès : {$crudClassName}");
        $this->output->writeln("Fichier : src/Interface/Admin/Crud/{$crudClassName}.php");
        $this->output->writeln("");
        $this->output->writeln("✨ Le CRUD est automatiquement disponible sur : /admin/crud/" . strtolower($entityName) . "s");
    }

    private function generateInputCode(string $propertyName, ?\ReflectionType $type): string
    {
        $label = ucfirst(str_replace('_', ' ', $propertyName));
        $typeString = ($type instanceof \ReflectionNamedType) ? $type->getName() : 'string';

        return match ($typeString) {
            'int' => "(new NumberInput('{$propertyName}', '{$label}'))->setRequired(true),",
            'bool' => "(new SelectInput('{$propertyName}', '{$label}'))->setOptions([0 => 'Non', 1 => 'Oui']),",
            'string' => strlen($propertyName) > 50 || str_contains($propertyName, 'content') || str_contains($propertyName, 'description')
                ? "(new TextareaInput('{$propertyName}', '{$label}'))->setRequired(true),"
                : "(new TextInput('{$propertyName}', '{$label}'))->setRequired(true),",
            default => "(new TextInput('{$propertyName}', '{$label}')),"
        };
    }

    private function generateColumnCode(string $propertyName, ?\ReflectionType $type): string
    {
        $label = ucfirst(str_replace('_', ' ', $propertyName));
        $typeString = ($type instanceof \ReflectionNamedType) ? $type->getName() : 'string';

        return match ($typeString) {
            'int' => "new NumberColumn('{$propertyName}', '{$label}'),",
            'bool' => "new BooleanColumn('{$propertyName}', '{$label}'),",
            default => "(new TextColumn('{$propertyName}', '{$label}'))->setLimit(50),"
        };
    }

    private function generateCrudResourceContent(
        string $entityName,
        string $className,
        string $inputsCode,
        string $columnsCode
    ): string {
        $plural = strtolower($entityName) . 's';

        return <<<PHP
<?php

declare(strict_types=1);

namespace App\Interface\Admin\Crud;

use App\Application\Service\Crud\CrudResource;
use App\Application\Service\Crud\Input\TextInput;
use App\Application\Service\Crud\Input\NumberInput;
use App\Application\Service\Crud\Input\SelectInput;
use App\Application\Service\Crud\Input\TextareaInput;
use App\Application\Service\Crud\Column\TextColumn;
use App\Application\Service\Crud\Column\NumberColumn;
use App\Application\Service\Crud\Column\BooleanColumn;
use App\Application\Service\Crud\Column\DateColumn;
use App\Domain\Entity\\{$entityName};

class {$className} extends CrudResource
{
    protected string \$entityClass = {$entityName}::class;
    protected string \$title = '{$entityName}s';
    protected string \$singularTitle = '{$entityName}';

    public function columns(): array
    {
        return [
            {$columnsCode}
        ];
    }

    public function inputs(): array
    {
        return [
            {$inputsCode}
        ];
    }
}

PHP;
    }
}
