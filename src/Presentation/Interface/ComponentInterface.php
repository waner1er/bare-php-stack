<?php

declare(strict_types=1);

namespace App\Presentation\Interface;

interface ComponentInterface
{
    public function render(): string;
}
