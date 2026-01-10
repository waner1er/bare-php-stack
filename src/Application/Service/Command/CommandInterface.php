<?php

declare(strict_types=1);

namespace App\Application\Service\Command;

interface CommandInterface
{
    public function execute(?string $name): void;
}
