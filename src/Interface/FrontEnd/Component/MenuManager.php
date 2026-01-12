<?php

declare(strict_types=1);

namespace App\Interface\FrontEnd\Component;

use App\Domain\Entity\Post;
use App\Domain\Entity\Product;

class MenuManager
{
    /**
     * Agrège tous les slugs des entités slugifiables et ajoute les liens d'archives/statique
     * @return array<int, array{slug: string, title: string, type: string}>
     */
    public static function getAllMenuSlugs(): array
    {
        $resources = [
            Post::class,
            Product::class,
        ];

        $slugs = [];

        // Ajouter les slugs des entités
        foreach ($resources as $resource) {
            if (is_subclass_of($resource, \App\Domain\Contract\SlugResourceInterface::class)) {
                $slugs = array_merge($slugs, $resource::getAllSlugsForMenu());
            }
        }

        // Note: Les pages statiques (contact, etc.) ne sont pas ajoutées ici
        // car elles ont leurs propres routes système définies dans le code

        return $slugs;
    }

    /**
     * Retourne tous les slugs disponibles qui ne sont pas encore dans le menu
     * @param array $currentMenuSlugs Les slugs déjà présents dans le menu
     * @return array
     */
    public static function getAvailableSlugs(array $currentMenuSlugs = []): array
    {
        $allSlugs = self::getAllMenuSlugs();

        if (empty($currentMenuSlugs)) {
            return $allSlugs;
        }

        return array_filter($allSlugs, function ($slug) use ($currentMenuSlugs) {
            return !in_array($slug['slug'], $currentMenuSlugs, true);
        });
    }
}
