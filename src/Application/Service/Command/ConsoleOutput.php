<?php

declare(strict_types=1);

namespace App\Application\Service\Command;

use App\Application\Service\Command\Interface\OutputInterface;

class ConsoleOutput implements OutputInterface
{
    public function writeln(string $message): void
    {
        echo $message . "\n";
    }
}
