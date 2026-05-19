<?php

declare(strict_types=1);

namespace App\Interface\FrontEnd\Component;

use App\Domain\Contract\MenuSlugProviderInterface;
use App\Infrastructure\Repository\PostRepository;

class MenuManager
{
    /**
     * @var MenuSlugProviderInterface[]
     */
    private array $slugProviders;

    public function __construct(?array $slugProviders = null)
    {
        $this->slugProviders = $slugProviders ?? [
            new PostRepository(),
        ];
    }

    /**
     * Agrège tous les slugs des sources disponibles
     * @return array<int, array{slug: string, title: string, type: string}>
     */
    public function getAllMenuSlugs(): array
    {
        $slugs = [];
        foreach ($this->slugProviders as $provider) {
            $slugs = array_merge($slugs, $provider->getMenuSlugs());
        }
        return $slugs;
    }

    /**
     * Retourne tous les slugs disponibles qui ne sont pas encore dans le menu
     * @param string[] $currentMenuSlugs
     * @return array<int, array{slug: string, title: string, type: string}>
     */
    public function getAvailableSlugs(array $currentMenuSlugs = []): array
    {
        $allSlugs = $this->getAllMenuSlugs();

        if (empty($currentMenuSlugs)) {
            return $allSlugs;
        }

        return array_filter($allSlugs, function ($slug) use ($currentMenuSlugs) {
            return !in_array($slug['slug'], $currentMenuSlugs, true);
        });
    }
}
