<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Le Domain est le cœur de l'application. Il ne doit dépendre de RIEN
 * en dehors de lui-même (ni Infrastructure, ni Application, ni Interface,
 * ni framework externe). C'est la règle fondamentale de la Clean Architecture.
 */
final class DomainPurityTest extends ArchitectureTestCase
{
    public static function domainFilesProvider(): iterable
    {
        yield from self::phpFilesIn('Domain');
    }

    #[DataProvider('domainFilesProvider')]
    public function test_domain_does_not_depend_on_outer_layers(string $file): void
    {
        $forbidden = [
            'App\\Infrastructure\\',
            'App\\Application\\',
            'App\\Interface\\',
        ];

        $violations = [];
        foreach ($this->extractUses($file) as $use) {
            foreach ($forbidden as $prefix) {
                if (str_starts_with($use, $prefix)) {
                    $violations[] = $use;
                }
            }
        }

        $this->assertSame(
            [],
            $violations,
            "Le fichier {$this->relPath($file)} importe des couches externes : " . implode(', ', $violations)
        );
    }

    #[DataProvider('domainFilesProvider')]
    public function test_domain_does_not_use_pdo_or_sql(string $file): void
    {
        $contents = $this->fileContents($file);

        $banned = [
            'PDO' => '/\bPDO\b/',
            'requête SQL inline' => '/(SELECT|INSERT|UPDATE|DELETE)\s+(\*|FROM|INTO|SET)/i',
        ];

        $hits = [];
        foreach ($banned as $label => $pattern) {
            if (preg_match($pattern, $contents)) {
                $hits[] = $label;
            }
        }
        $this->assertSame(
            [],
            $hits,
            "Le fichier {$this->relPath($file)} contient des éléments interdits dans le Domain : " . implode(', ', $hits)
        );
    }
}
