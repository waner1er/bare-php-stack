<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * La couche Application orchestre le Domain. Elle peut dépendre du Domain
 * mais ne doit pas dépendre de l'Infrastructure ni de l'Interface (UI).
 */
final class ApplicationLayerTest extends ArchitectureTestCase
{
    public static function applicationFilesProvider(): iterable
    {
        yield from self::phpFilesIn('Application');
    }

    #[DataProvider('applicationFilesProvider')]
    public function test_application_does_not_depend_on_infrastructure_or_interface(string $file): void
    {
        $forbidden = [
            'App\\Infrastructure\\',
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
            "Le fichier {$this->relPath($file)} couple Application à une couche externe : " . implode(', ', $violations)
        );
    }
}
