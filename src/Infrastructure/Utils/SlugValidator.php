<?php

declare(strict_types=1);

namespace App\Infrastructure\Utils;

use App\Infrastructure\Repository\PostRepository;
use App\Infrastructure\Repository\MenuItemRepository;

class SlugValidator
{
    /**
     * Slugs réservés pour les pages statiques et routes système
     */
    private const RESERVED_SLUGS = [
        'contact',
        'archive',
        'login',
        'register',
        'logout',
        'posts',
        'admin',
    ];

    /**
     * Vérifie si un slug est disponible globalement
     */
    public static function isSlugAvailable(string $slug, ?int $excludePostId = null): bool
    {
        // Vérifier si le slug est réservé
        if (in_array($slug, self::RESERVED_SLUGS)) {
            return false;
        }

        // Vérifier si le slug existe déjà dans les posts
        $postRepo = new PostRepository();
        $posts = $postRepo->findAll();
        foreach ($posts as $post) {
            if ($post->getSlug() === $slug && $post->getId() !== $excludePostId) {
                return false;
            }
        }

        // Vérifier si le slug existe déjà dans les menu items
        $menuRepo = new MenuItemRepository();
        $menuItems = $menuRepo->findAll();
        foreach ($menuItems as $item) {
            if ($item->getSlug() === $slug) {
                return false;
            }
        }

        return true;
    }

    /**
     * Retourne la liste des slugs réservés
     */
    public static function getReservedSlugs(): array
    {
        return self::RESERVED_SLUGS;
    }

    /**
     * Retourne tous les slugs utilisés sur le site
     */
    public static function getAllUsedSlugs(): array
    {
        $slugs = self::RESERVED_SLUGS;

        // Ajouter les slugs des posts
        $postRepo = new PostRepository();
        $posts = $postRepo->findAll();
        foreach ($posts as $post) {
            $slugs[] = $post->getSlug();
        }

        // Ajouter les slugs des menu items
        $menuRepo = new MenuItemRepository();
        $menuItems = $menuRepo->findAll();
        foreach ($menuItems as $item) {
            $slugs[] = $item->getSlug();
        }

        return array_unique($slugs);
    }
}
