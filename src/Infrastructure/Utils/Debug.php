<?php

declare(strict_types=1);

namespace App\Infrastructure\Utils;

class Debug
{
    public static function enable(): void
    {
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);

        set_exception_handler(function ($e) {
            echo "<h1>Exception: " . get_class($e) . "</h1>";
            echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
            echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        });

        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            echo "<h1>Error [$errno]</h1>";
            echo "<p><strong>Message:</strong> $errstr</p>";
            echo "<p><strong>File:</strong> $errfile:$errline</p>";
            return false;
        });
    }

    public static function disable(): void
    {
        ini_set('display_errors', '0');
        ini_set('display_startup_errors', '0');
        error_reporting(0);
    }
}
