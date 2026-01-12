<?php

declare(strict_types=1);

namespace App\Domain\Contract;

interface SlugResourceInterface
{
    /**
     * Retourne un tableau de slugs pour le menu :
     * [ ['slug' => ..., 'title' => ..., 'type' => ...], ... ]
     */
    public static function getAllSlugsForMenu(): array;
}
