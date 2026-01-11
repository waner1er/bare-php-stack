<?php

declare(strict_types=1);

namespace App\Interface\FrontEnd\Component;

use App\Infrastructure\Auth\Auth;
use App\Infrastructure\Blade\Blade;
use App\Domain\Entity\Post;

class NavMenu
{
    private array $menuItems = [];

    public function __construct(array $data = [])
    {
        $this->buildMenu();
    }

    private function buildMenu(): void
    {
        // Menu de base
        $this->menuItems = [
            ['label' => 'Accueil', 'route' => 'home'],
        ];

        // Ajouter les posts marqués pour le menu
        $posts = Post::getMenuItems();

        foreach ($posts as $post) {
            $this->menuItems[] = [
                'label' => $post->getTitle(),
                'url' => '/posts/' . $post->getSlug(),
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
