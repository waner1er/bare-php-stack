<?php

declare(strict_types=1);

namespace App\Application\Service\Command;

use App\Application\Service\Command\CommandInterface;

class MakeModelCommand implements CommandInterface
{
    public function execute(?string $name, array $options = []): void
    {
        if (!$name) {
            echo "Usage: minor make:model ModelName [--migration]\n";
            exit(1);
        }

        $createMigration = in_array('--migration', $options);

        $className = ucfirst($name);
        $filePath = DOMAIN_PATH . "/Entity/{$className}.php";

        if (file_exists($filePath)) {
            echo "Le model $className existe déjà.\n";
            exit(1);
        }

        echo "Nom de la table (par défaut: " . strtolower($className) . "s): ";
        $tableName = trim(fgets(STDIN));
        if (empty($tableName)) {
            $tableName = strtolower($className) . 's';
        }

        $properties = [['type' => 'int', 'name' => 'id']];
        $propertyLines = "    public int \$id;\n";
        $primaryKey = 'id';
        $foreignKeys = [];

        echo "Entrez les propriétés supplémentaires (appuyez sur Entrée sans rien saisir pour terminer):\n";

        while (true) {
            echo "Nom de la propriété: ";
            $propertyName = trim(fgets(STDIN));

            if (empty($propertyName)) {
                break;
            }

            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $propertyName)) {
                echo "Nom de propriété invalide. Essayez encore.\n";
                continue;
            }

            echo "\nType de la propriété:\n";
            echo "  1. int\n";
            echo "  2. string\n";
            echo "  3. bool\n";
            echo "  4. float\n";
            echo "  5. text (TINYTEXT, TEXT, MEDIUMTEXT, LONGTEXT)\n";
            echo "Choix: ";
            $typeChoice = trim(fgets(STDIN));


            $type = match ($typeChoice) {
                '1' => 'int',
                '2' => 'string',
                '3' => 'bool',
                '4' => 'float',
                '5' => 'text',
                default => 'string'
            };

            $textSize = null;
            if ($type === 'text') {
                echo "Choisissez la taille du champ text :\n";
                echo "  1. TINYTEXT (255 caractères)\n";
                echo "  2. TEXT (65 535 caractères)\n";
                echo "  3. MEDIUMTEXT (16 Mo)\n";
                echo "  4. LONGTEXT (4 Go)\n";
                echo "Choix (défaut: 2): ";
                $textChoice = trim(fgets(STDIN));
                $textSize = match ($textChoice) {
                    '1' => 'tinytext',
                    '2' => 'text',
                    '3' => 'mediumtext',
                    '4' => 'longtext',
                    default => 'text'
                };
            }

            $isForeignKey = false;
            if ($type === 'int' && str_ends_with($propertyName, '_id')) {
                echo "Est-ce une clé étrangère (foreign key)? (o/n): ";
                $foreignKeyResponse = trim(fgets(STDIN));

                if (strtolower($foreignKeyResponse) === 'o') {
                    $isForeignKey = true;
                    $modelName = str_replace('_id', '', $propertyName);
                    $referencedTable = $modelName . 's';

                    echo "Nom de la table référencée (par défaut: {$referencedTable}): ";
                    $tableInput = trim(fgets(STDIN));
                    if (!empty($tableInput)) {
                        $referencedTable = $tableInput;
                    }

                    $foreignKeys[] = [
                        'column' => $propertyName,
                        'references' => $referencedTable
                    ];
                }
            }

            $properties[] = ['type' => $type, 'name' => $propertyName, 'textSize' => $textSize];
            $phpType = $type === 'text' ? 'string' : $type;
            $propertyLines .= "    public {$phpType} \$" . $propertyName . ";\n";
        }

        $methodsLines = "";
        foreach ($properties as $property) {
            $type = $property['type'];
            $name = $property['name'];
            $phpType = $type === 'text' ? 'string' : $type;

            $camelCaseName = str_replace('_', '', ucwords($name, '_'));

            $methodsLines .= "\n    public function get{$camelCaseName}(): {$phpType}\n";
            $methodsLines .= "    {\n";
            $methodsLines .= "        return \$this->{$name};\n";
            $methodsLines .= "    }\n";

            $methodsLines .= "\n    public function set{$camelCaseName}({$phpType} \${$name}): void\n";
            $methodsLines .= "    {\n";
            $methodsLines .= "        \$this->{$name} = \${$name};\n";
            $methodsLines .= "    }\n";
        }

        foreach ($foreignKeys as $fk) {
            $columnName = $fk['column'];
            $referencedTable = $fk['references'];

            $relationName = str_replace('_id', '', $columnName);
            $modelName = ucfirst($relationName);

            $methodsLines .= "\n    public function {$relationName}(): ?{$modelName}\n";
            $methodsLines .= "    {\n";
            $methodsLines .= "        return {$modelName}::find(\$this->{$columnName});\n";
            $methodsLines .= "    }\n";
        }

        $template = <<<PHP
<?php
        
declare(strict_types=1);
        
namespace App\Domain\Entity;
        
use App\Domain\Abstract\Model;
        
class {$className} extends Model
{
    protected static string \$table = '{$tableName}';
    protected static string \$primaryKey = '{$primaryKey}';
        
{$propertyLines}
    public function __construct(array \$data = [])
    {
        foreach (\$data as \$key => \$value) {
            if (property_exists(\$this, \$key)) {
                \$this->\$key = \$value;
            }
        }
    }
{$methodsLines}
}
        
PHP;

        file_put_contents($filePath, $template);
        echo "Modèle $className créé dans src/Domain/Entity.\n";

        if ($createMigration) {
            $this->createMigration($className, $tableName, $properties, $foreignKeys);
        }
    }

    private function createMigration(string $className, string $tableName, array $properties, array $foreignKeys): void
    {
        $migrationsDir = MIGRATIONS_PATH . "/files";

        if (!is_dir($migrationsDir)) {
            mkdir($migrationsDir, 0755, true);
        }

        $sqlColumns = [];
        $foreignKeyConstraints = [];

        foreach ($properties as $property) {
            $name = $property['name'];
            $type = $property['type'];
            $textSize = $property['textSize'] ?? null;

            $sqlType = match ($type) {
                'int' => 'INT',
                'string' => 'VARCHAR(255)',
                'bool' => 'TINYINT(1)',
                'float' => 'DECIMAL(10,2)',
                'text' => match ($textSize) {
                    'tinytext' => 'TINYTEXT',
                    'mediumtext' => 'MEDIUMTEXT',
                    'longtext' => 'LONGTEXT',
                    default => 'TEXT'
                },
                default => 'VARCHAR(255)'
            };

            if ($name === 'id') {
                $sqlColumns[] = "id INT AUTO_INCREMENT PRIMARY KEY";
            } else {
                $sqlColumns[] = "{$name} {$sqlType} NOT NULL";
            }
        }

        foreach ($foreignKeys as $fk) {
            $foreignKeyConstraints[] = "FOREIGN KEY ({$fk['column']}) REFERENCES {$fk['references']}(id) ON DELETE CASCADE";
        }

        $allColumns = array_merge($sqlColumns, $foreignKeyConstraints);
        $columnsStr = implode(",\n    ", $allColumns);

        $timestamp = date('Y_m_d_His');
        $migrationFile = $migrationsDir . "/{$timestamp}_create_{$tableName}_table.php";

        $migrationContent = <<<PHP
<?php

// Migration: Create {$tableName} table
// Generated: {$timestamp}

return "CREATE TABLE IF NOT EXISTS {$tableName} (
    {$columnsStr}
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

PHP;

        file_put_contents($migrationFile, $migrationContent);
        echo "Migration créée: migrations/files/" . basename($migrationFile) . "\n";
    }
}
