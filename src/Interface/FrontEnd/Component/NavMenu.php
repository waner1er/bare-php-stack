<?php

declare(strict_types=1);

namespace App\Interface\FrontEnd\Component;

use App\Infrastructure\Auth\Auth;
use App\Infrastructure\Blade\Blade;
use App\Domain\Entity\MenuItem;

class NavMenu
{
    private array $menuItems = [];

    public function __construct(array $data = [])
    {
        $this->buildMenu();
    }

    private function buildMenu(): void
    {
        // Récupérer les items de menu visibles depuis la table menu_items
        $menuItems = MenuItem::getVisibleItems();

        $this->menuItems = [];
        foreach ($menuItems as $item) {
            $slug = $item->getSlug();
            $type = $item->getType();
            $categoryId = $item->getCategoryId();
            $entityType = $item->getEntityType();

            // Générer l'URL en fonction du type
            if ($type === 'archive') {
                $entityPath = strtolower($entityType) . 's'; // Post -> posts, Product -> products

                if ($categoryId) {
                    // Archive avec catégorie spécifique : /posts/{category-slug} ou /products/{category-slug}
                    $category = \App\Domain\Entity\Category::find($categoryId);
                    $url = $category ? '/' . $entityPath . '/' . $category->getSlug() : '/' . $entityPath;
                } else {
                    // Archive sans catégorie : /posts (tous les items)
                    $url = '/' . $entityPath;
                }
            } else {
                // Pour les items individuels
                $url = match ($type) {
                    'post' => '/' . $slug,  // Posts dans le menu = URL à la racine
                    'static' => $slug === 'accueil' ? '/' : '/' . $slug,
                    default => '/' . $slug,
                };
            }

            $this->menuItems[] = [
                'label' => $item->getLabel(),
                'url' => $url,
                'type' => $type,
            ];
        }
    }

    public function render(): string
    {
        $blade = new Blade(INTERFACE_PATH . '/FrontEnd/View', STORAGE_PATH . '/cache');

        return $blade->render('components.navmenu', [
            'isAuthenticated' => Auth::check(),
            'user' => Auth::user(),
            'menuItems' => $this->menuItems,
        ]);
    }
}
