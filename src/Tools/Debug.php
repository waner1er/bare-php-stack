<?php

declare(strict_types=1);

namespace App\Tools;

class Debug
{
    public static function enable(): void
    {
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);
    }

    public static function disable(): void
    {
        ini_set('display_errors', '0');
        ini_set('display_startup_errors', '0');
        error_reporting(0);
    }
}
