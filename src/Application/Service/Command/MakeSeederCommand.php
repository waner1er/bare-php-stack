<?php

declare(strict_types=1);

namespace App\Application\Service\Command;

use App\Application\Service\Command\Interface\CommandInterface;
use App\Application\Service\Command\Interface\OutputInterface;

class MakeSeederCommand implements CommandInterface
{
    private OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function execute(?string $name, array $options = []): void
    {
        if (!$name) {
            $this->output->writeln("Usage: minor make:seeder ModelName");
            return;
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
            $this->output->writeln("Le seeder {$seederName} existe déjà.");
            return;
        }

        if (!file_exists($modelPath)) {
            $this->output->writeln("Le modèle {$modelName} n'existe pas.");
            return;
        }

        $properties = $this->parseModel($modelPath);

        if (empty($properties)) {
            echo "Aucune propriété trouvée dans le modèle {$modelName}.\n";
            exit(1);
        }

        $content = $this->generateSeeder($modelName, $properties);

        file_put_contents($filePath, $content);
        echo "✓ Seeder '{$seederName}' créé dans migrations/seeders/\n";
    }

    private function parseModel(string $modelPath): array
    {
        $content = file_get_contents($modelPath);
        $properties = [];

        preg_match('/protected static string \$table = \'([^\']+)\'/', $content, $tableMatch);
        $tableName = $tableMatch[1] ?? '';

        preg_match_all('/public (int|string|bool|float|\?string) \$([a-zA-Z_]+);/', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $type = str_replace('?', '', $match[1]);
            $name = $match[2];

            if ($name !== 'id') {
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
        $properties = $data['properties'];

        $fakerArray = [];
        foreach ($properties as $prop) {
            $fakerArray[] = "        '{$prop['name']}' => " . $this->getFakerMethod($prop['name'], $prop['type']);
        }
        $fakerArrayStr = implode(",\n", $fakerArray);

        return <<<PHP
<?php

use Faker\Factory;
use App\Domain\Entity\\{$modelName};

return function() {
    \$faker = Factory::create();
    \$count = 20;

    for (\$i = 0; \$i < \$count; \$i++) {
        \$model = new {$modelName}([
{$fakerArrayStr}
        ]);
        \$model->save();
    }

    echo "  ✓ {\$count} {$modelName} créés\\n";
};

PHP;
    }

    private function getFakerMethod(string $propertyName, string $type): string
    {
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

        return match ($type) {
            'int' => '$faker->numberBetween(1, 100)',
            'float' => '$faker->randomFloat(2, 0, 1000)',
            'bool' => '$faker->boolean()',
            'string' => '$faker->word()',
            default => '$faker->word()',
        };
    }
}
