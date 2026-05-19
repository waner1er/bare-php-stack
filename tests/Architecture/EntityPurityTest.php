<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Les entités du Domain sont des POPO : pas d'héritage de Model,
 * pas de méthodes statiques de fetch (::find, ::all, etc.), pas de save/delete.
 * La persistance passe exclusivement par les repositories.
 */
final class EntityPurityTest extends ArchitectureTestCase
{
    public static function entityFilesProvider(): iterable
    {
        yield from self::phpFilesIn('Domain/Entity');
    }

    #[DataProvider('entityFilesProvider')]
    public function test_entity_does_not_extend_an_orm_base_class(string $file): void
    {
        $contents = $this->fileContents($file);
        $hasOrmParent = (bool) preg_match('/\bextends\s+(Model|AbstractModel|AbstractRepository)\b/', $contents);
        $this->assertFalse(
            $hasOrmParent,
            "L'entité {$this->relPath($file)} ne doit pas hériter d'une classe d'ORM."
        );
    }

    #[DataProvider('entityFilesProvider')]
    public function test_entity_has_no_persistence_methods(string $file): void
    {
        $contents = $this->fileContents($file);

        $banned = [
            'save()' => '/\bpublic\s+(static\s+)?function\s+save\s*\(/',
            'delete()' => '/\bpublic\s+(static\s+)?function\s+delete\s*\(/',
            '::find / ::all (static fetch)' => '/\bpublic\s+static\s+function\s+(find|findBy|findAll|findBySlug|all)\s*\(/',
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
            "L'entité {$this->relPath($file)} expose des méthodes de persistance : " . implode(', ', $hits)
        );
    }
}
