<?php

declare(strict_types=1);

namespace App\Domain\Contract;

interface MenuSlugProviderInterface
{
    /**
     * Retourne un tableau de slugs pour le menu :
     * [ ['slug' => ..., 'title' => ..., 'type' => ...], ... ]
     *
     * @return array<int, array{slug: string, title: string, type: string}>
     */
    public function getMenuSlugs(): array;
}
