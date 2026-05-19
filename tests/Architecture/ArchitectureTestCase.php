<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPUnit\Framework\TestCase;

abstract class ArchitectureTestCase extends TestCase
{
    protected const SRC = __DIR__ . '/../../src';

    /** @return iterable<string, array{string}> */
    protected static function phpFilesIn(string $relativePath): iterable
    {
        $base = realpath(self::SRC . '/' . $relativePath);
        if ($base === false) {
            return;
        }
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS)
        );
        $root = realpath(__DIR__ . '/../..');
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $path = $file->getPathname();
                $label = $root ? str_replace($root . '/', '', $path) : $path;
                yield $label => [$path];
            }
        }
    }

    /** @return string[] */
    protected function extractUses(string $filePath): array
    {
        $source = file_get_contents($filePath);
        if ($source === false) {
            return [];
        }
        preg_match_all('/^\s*use\s+([^\s;]+)\s*;/m', $source, $matches);
        return $matches[1] ?? [];
    }

    protected function fileContents(string $filePath): string
    {
        return (string) file_get_contents($filePath);
    }

    protected function relPath(string $absolutePath): string
    {
        $root = realpath(__DIR__ . '/../..');
        return $root ? str_replace($root . '/', '', $absolutePath) : $absolutePath;
    }
}
