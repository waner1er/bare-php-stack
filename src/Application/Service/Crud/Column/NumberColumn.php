<?php

declare(strict_types=1);

namespace App\Application\Service\Crud\Column;

class NumberColumn extends AbstractColumn
{
    public function renderCell(mixed $value): string
    {
        $number = htmlspecialchars((string)$value);
        return "<td class=\"crud-table-cell crud-table-cell-number\">{$number}</td>";
    }
}
