<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Les implémentations de repositories vivent en Infrastructure
 * mais doivent implémenter une interface définie dans le Domain.
 * C'est le Dependency Inversion Principle appliqué à la persistance.
 */
final class RepositoryInversionTest extends ArchitectureTestCase
{
    public static function repositoryImplProvider(): iterable
    {
        yield from self::phpFilesIn('Infrastructure/Repository');
    }

    #[DataProvider('repositoryImplProvider')]
    public function test_repository_implements_a_domain_interface(string $file): void
    {
        $uses = $this->extractUses($file);

        $hasDomainInterface = false;
        foreach ($uses as $use) {
            if (str_starts_with($use, 'App\\Domain\\Repository\\')
                && str_ends_with($use, 'RepositoryInterface')) {
                $hasDomainInterface = true;
                break;
            }
        }

        $this->assertTrue(
            $hasDomainInterface,
            "Le repo {$this->relPath($file)} doit implémenter une interface de App\\Domain\\Repository."
        );
    }

    public static function domainRepositoryInterfacesProvider(): iterable
    {
        yield from self::phpFilesIn('Domain/Repository');
    }

    #[DataProvider('domainRepositoryInterfacesProvider')]
    public function test_domain_repository_is_an_interface(string $file): void
    {
        $contents = $this->fileContents($file);
        $this->assertMatchesRegularExpression(
            '/\binterface\s+\w+RepositoryInterface\b/',
            $contents,
            "Le fichier {$this->relPath($file)} doit déclarer une interface se terminant par RepositoryInterface."
        );
    }
}
