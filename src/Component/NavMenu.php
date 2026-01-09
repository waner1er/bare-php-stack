<?php

namespace App\Component;

use App\Tools\Blade;
use App\Tools\Auth;

class NavMenu
{
    public function __construct(array $data = [])
    {
        // Initialiser les propriétés du composant
    }

    public function render(): string
    {
        $views = __DIR__ . '/../../resources/views';
        $cache = __DIR__ . '/../../storage/cache';
        $blade = new Blade($views, $cache);

        return $blade->render('components.navmenu', [
            'isAuthenticated' => Auth::check(),
            'user' => Auth::user(),
        ]);
    }
}
