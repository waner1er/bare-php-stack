<?php

declare(strict_types=1);

namespace App\Application\Service\Crud\Column;

class TextColumn extends AbstractColumn
{
    protected ?int $limit = null;

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function renderCell(mixed $value): string
    {
        $text = htmlspecialchars((string)$value);

        if ($this->limit && strlen($text) > $this->limit) {
            $text = substr($text, 0, $this->limit) . '...';
        }

        return "<td class=\"crud-table-cell\">{$text}</td>";
    }
}
