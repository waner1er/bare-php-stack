<?php

declare(strict_types=1);

namespace App\Controller;

class BaseController
{
    protected function render(string $view, array $params = []): void
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        require_once __DIR__ . '/../../src/Tools/BladeInstance.php';
        echo $blade->render($view, $params);
    }
}
