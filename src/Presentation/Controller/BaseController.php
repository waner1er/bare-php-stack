<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Infrastructure\Blade\Blade;

class BaseController
{
    private static ?Blade $blade = null;

    protected function render(string $view, array $params = []): void
    {
        if (self::$blade === null) {
            $views = __DIR__ . '/../View';
            $cache = __DIR__ . '/../../../storage/cache';
            self::$blade = new Blade($views, $cache);
        }
        echo self::$blade->render($view, $params);
    }
}
