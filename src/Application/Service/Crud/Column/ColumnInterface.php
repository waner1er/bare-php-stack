<?php

declare(strict_types=1);

namespace App\Application\Service\Crud\Column;

interface ColumnInterface
{
    public function getName(): string;
    public function getLabel(): string;
    public function renderHeader(): string;
    public function renderCell(mixed $value): string;
}
