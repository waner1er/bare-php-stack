<?php

declare(strict_types=1);

namespace App\Infrastructure\Interface;

interface MiddlewareInterface
{
    public static function handle(): void;
}
