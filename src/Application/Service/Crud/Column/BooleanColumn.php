<?php

declare(strict_types=1);

namespace App\Application\Service\Crud\Column;

class BooleanColumn extends AbstractColumn
{
    public function renderCell(mixed $value): string
    {
        $badge = $value
            ? '<span class="crud-badge crud-badge-success">Oui</span>'
            : '<span class="crud-badge crud-badge-danger">Non</span>';

        return "<td class=\"crud-table-cell crud-table-cell-boolean\">{$badge}</td>";
    }
}
