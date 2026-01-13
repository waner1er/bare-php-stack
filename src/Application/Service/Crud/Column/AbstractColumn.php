<?php

declare(strict_types=1);

namespace App\Application\Service\Crud\Column;

abstract class AbstractColumn implements ColumnInterface
{
    protected string $name;
    protected string $label;
    protected bool $sortable = false;

    public function __construct(string $name, string $label)
    {
        $this->name = $name;
        $this->label = $label;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setSortable(bool $sortable): self
    {
        $this->sortable = $sortable;
        return $this;
    }

    public function renderHeader(): string
    {
        $sortableClass = $this->sortable ? 'crud-sortable' : '';
        return "<th class=\"crud-table-header {$sortableClass}\" data-column=\"{$this->name}\">{$this->label}</th>";
    }

    abstract public function renderCell(mixed $value): string;
}
