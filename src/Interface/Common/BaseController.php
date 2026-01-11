<?php

declare(strict_types=1);

namespace App\Interface\Common;

use App\Infrastructure\Blade\Blade;

class BaseController
{
    private static ?Blade $blade = null;

    protected function render(string $view, array $params = []): void
    {
        if (self::$blade === null) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $caller = $trace[1]['class'] ?? '';
            if (preg_match('/Interface\\\\(\\w+)\\\\Controller/', $caller, $matches)) {
                $interface = $matches[1];
                $views = INTERFACE_PATH . '/' . $interface . '/View';
            } else {
                $views = INTERFACE_PATH;
            }
            $cache = CACHE_PATH;
            self::$blade = new Blade($views, $cache);
        }
        echo self::$blade->render($view, $params);
    }
}
