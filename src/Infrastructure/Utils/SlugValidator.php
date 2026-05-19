<?php

declare(strict_types=1);

namespace App\Infrastructure\Utils;

use App\Infrastructure\Repository\MenuItemRepository;
use App\Infrastructure\Repository\PostRepository;

class SlugValidator
{
    private const RESERVED_SLUGS = [
        'contact',
        'archive',
        'login',
        'register',
        'logout',
        'posts',
        'admin',
    ];

    public static function isSlugAvailable(string $slug, ?int $excludePostId = null): bool
    {
        if (in_array($slug, self::RESERVED_SLUGS)) {
            return false;
        }

        $postRepo = new PostRepository();
        foreach ($postRepo->findAll() as $post) {
            if ($post->getSlug() === $slug && $post->getId() !== $excludePostId) {
                return false;
            }
        }

        $menuRepo = new MenuItemRepository();
        foreach ($menuRepo->findAll() as $item) {
            if ($item->getSlug() === $slug) {
                return false;
            }
        }

        return true;
    }

    public static function getReservedSlugs(): array
    {
        return self::RESERVED_SLUGS;
    }

    public static function getAllUsedSlugs(): array
    {
        $slugs = self::RESERVED_SLUGS;

        $postRepo = new PostRepository();
        foreach ($postRepo->findAll() as $post) {
            $slugs[] = $post->getSlug();
        }

        $menuRepo = new MenuItemRepository();
        foreach ($menuRepo->findAll() as $item) {
            $slugs[] = $item->getSlug();
        }

        return array_unique($slugs);
    }
}
