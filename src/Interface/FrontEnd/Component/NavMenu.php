<?php

declare(strict_types=1);

namespace App\Interface\FrontEnd\Component;

use App\Domain\Repository\CategoryRepositoryInterface;
use App\Domain\Repository\MenuItemRepositoryInterface;
use App\Infrastructure\Auth\Auth;
use App\Infrastructure\Blade\Blade;
use App\Infrastructure\Repository\CategoryRepository;
use App\Infrastructure\Repository\MenuItemRepository;

class NavMenu
{
    private array $menuItems = [];

    public function __construct(
        private MenuItemRepositoryInterface $menuItemRepository = new MenuItemRepository(),
        private CategoryRepositoryInterface $categoryRepository = new CategoryRepository(),
    ) {
        $this->buildMenu();
    }

    private function buildMenu(): void
    {
        $menuItems = $this->menuItemRepository->findVisible();

        $this->menuItems = [];
        foreach ($menuItems as $item) {
            $slug = $item->getSlug();
            $type = $item->getType();
            $categoryId = $item->getCategoryId();
            $entityType = $item->getEntityType();

            if ($type === 'archive') {
                $entityPath = strtolower($entityType) . 's';

                if ($categoryId) {
                    $category = $this->categoryRepository->find($categoryId);
                    $url = $category ? '/' . $entityPath . '/' . $category->getSlug() : '/' . $entityPath;
                } else {
                    $url = '/' . $entityPath;
                }
            } else {
                $url = match ($type) {
                    'post' => '/' . $slug,
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
