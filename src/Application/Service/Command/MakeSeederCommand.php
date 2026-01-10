<?php

declare(strict_types=1);

namespace App\Application\Service\Command;

use App\Application\Service\Command\CommandInterface;

class MakeSeederCommand implements CommandInterface
{
    public function execute(?string $name, array $options = []): void
    {
        if (!$name) {
            echo "Usage: minor make:seeder ModelName\n";
            exit(1);
        }

        $modelName = ucfirst($name);
        $seederName = $modelName . 'Seeder';

        $seedersDir = MIGRATIONS_PATH . "/seeders";
        $filePath = $seedersDir . "/{$seederName}.php";

        $modelPath = DOMAIN_PATH . "/Entity/{$modelName}.php";

        if (!is_dir($seedersDir)) {
            mkdir($seedersDir, 0755, true);
        }

        if (file_exists($filePath)) {
            echo "Le seeder {$seederName} existe déjà.\n";
            exit(1);
        }

        // Vérifier si le modèle existe
        if (!file_exists($modelPath)) {
            echo "Le modèle {$modelName} n'existe pas.\n";
            exit(1);
        }

        // Analyser le modèle pour extraire les propriétés
        $properties = $this->parseModel($modelPath);

        if (empty($properties)) {
            echo "Aucune propriété trouvée dans le modèle {$modelName}.\n";
            exit(1);
        }

        // Générer le code du seeder
        $content = $this->generateSeeder($modelName, $properties);

        file_put_contents($filePath, $content);
        echo "✓ Seeder '{$seederName}' créé dans migrations/seeders/\n";
    }

    private function parseModel(string $modelPath): array
    {
        $content = file_get_contents($modelPath);
        $properties = [];

        // Extraire la table name
        preg_match('/protected static string \$table = \'([^\']+)\'/', $content, $tableMatch);
        $tableName = $tableMatch[1] ?? '';

        // Extraire les propriétés publiques
        preg_match_all('/public (int|string|bool|float|\?string) \$([a-zA-Z_]+);/', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $type = str_replace('?', '', $match[1]); // Retirer le ? pour les types nullable
            $name = $match[2];

            if ($name !== 'id') { // Ignorer l'ID qui est auto-increment
                $properties[] = [
                    'name' => $name,
                    'type' => $type,
                ];
            }
        }

        return ['table' => $tableName, 'properties' => $properties];
    }

    private function generateSeeder(string $modelName, array $data): string
    {
        $tableName = $data['table'];
        $properties = $data['properties'];

        // Générer les colonnes pour l'INSERT
        $columns = array_map(fn($p) => $p['name'], $properties);
        $columnsStr = implode(', ', $columns);
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));

        // Générer les valeurs Faker
        $fakerValues = [];
        foreach ($properties as $prop) {
            $fakerValues[] = $this->getFakerMethod($prop['name'], $prop['type']);
        }
        $fakerValuesStr = implode(",\n            ", $fakerValues);

        return <<<PHP
<?php

// Seeder: {$modelName}
// Génère des données pour la table '{$tableName}'

use Faker\Factory;
use App\Infrastructure\Database;

return function() {
    \$faker = Factory::create();
    \$pdo = Database::getConnection();
    
    \$count = 20;
    
    for (\$i = 0; \$i < \$count; \$i++) {
        \$stmt = \$pdo->prepare("INSERT INTO {$tableName} ({$columnsStr}) VALUES ({$placeholders})");
        \$stmt->execute([
            {$fakerValuesStr}
        ]);
    }
    
    echo "  ✓ {\$count} {$tableName} créés\\n";
};

PHP;
    }

    private function getFakerMethod(string $propertyName, string $type): string
    {
        // Mapping intelligent basé sur le nom de la propriété
        $lowerName = strtolower($propertyName);

        if (str_contains($lowerName, 'email')) {
            return '$faker->email()';
        }
        if (str_contains($lowerName, 'name') || str_contains($lowerName, 'username')) {
            return '$faker->name()';
        }
        if (str_contains($lowerName, 'title')) {
            return '$faker->sentence(6)';
        }
        if (str_contains($lowerName, 'content') || str_contains($lowerName, 'description') || str_contains($lowerName, 'body')) {
            return '$faker->paragraphs(3, true)';
        }
        if (str_contains($lowerName, 'password')) {
            return 'password_hash(\'password\', PASSWORD_DEFAULT)';
        }
        if (str_contains($lowerName, 'phone')) {
            return '$faker->phoneNumber()';
        }
        if (str_contains($lowerName, 'address')) {
            return '$faker->address()';
        }
        if (str_contains($lowerName, 'city')) {
            return '$faker->city()';
        }
        if (str_contains($lowerName, 'country')) {
            return '$faker->country()';
        }
        if (str_contains($lowerName, 'url') || str_contains($lowerName, 'website')) {
            return '$faker->url()';
        }

        // Mapping par type si aucun pattern n'est trouvé
        return match ($type) {
            'int' => '$faker->numberBetween(1, 100)',
            'float' => '$faker->randomFloat(2, 0, 1000)',
            'bool' => '$faker->boolean()',
            'string' => '$faker->word()',
            default => '$faker->word()',
        };
    }
}
