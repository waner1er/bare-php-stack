<?php

declare(strict_types=1);

namespace App\Application\Service\Command\Interface;

interface CommandInterface
{
    public function execute(?string $name, array $options = []): void;
}
