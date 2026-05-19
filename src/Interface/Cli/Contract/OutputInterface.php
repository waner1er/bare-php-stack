<?php

declare(strict_types=1);

namespace App\Interface\Cli\Contract;

interface OutputInterface
{
    public function writeln(string $message): void;
}
