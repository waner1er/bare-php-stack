<?php

declare(strict_types=1);

namespace App\Application\Service\Crud\Column;

class DateColumn extends AbstractColumn
{
    protected string $format = 'd/m/Y H:i';

    public function setFormat(string $format): self
    {
        $this->format = $format;
        return $this;
    }

    public function renderCell(mixed $value): string
    {
        if (empty($value)) {
            return "<td class=\"crud-table-cell\">-</td>";
        }

        $date = is_string($value) ? new \DateTime($value) : $value;
        $formatted = $date->format($this->format);

        return "<td class=\"crud-table-cell crud-table-cell-date\">{$formatted}</td>";
    }
}
