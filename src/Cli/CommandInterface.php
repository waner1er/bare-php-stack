<?php

namespace App\Cli;

interface CommandInterface
{
    public function execute(?string $name): void;
}
