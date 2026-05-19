<?php

declare(strict_types=1);

namespace App\Interface\Cli\Contract;

interface CommandInterface
{
    public function execute(?string $name, array $options = []): void;
}
