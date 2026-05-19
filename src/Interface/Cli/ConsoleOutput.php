<?php

declare(strict_types=1);

namespace App\Interface\Cli;

use App\Interface\Cli\Contract\OutputInterface;

class ConsoleOutput implements OutputInterface
{
    public function writeln(string $message): void
    {
        echo $message . "\n";
    }
}
