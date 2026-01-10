<?php

declare(strict_types=1);

namespace App\Application\Service\Command\Interface;

interface OutputInterface
{
    public function writeln(string $message): void;
}
