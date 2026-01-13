<?php

declare(strict_types=1);

namespace App\Application\Service\Crud\Column;

class RelationColumn implements ColumnInterface
{
    private string $name;
    private string $label;
    private string $relationMethod;
    private string $displayAttribute;

    public function __construct(
        string $name,
        string $label,
        string $relationMethod,
        string $displayAttribute = 'name'
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->relationMethod = $relationMethod;
        $this->displayAttribute = $displayAttribute;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function renderHeader(): string
    {
        return "<th class=\"crud-table-header\">{$this->label}</th>";
    }

    public function renderCell($value): string
    {
        // $value est l'entité parente, pas la valeur directe
        // On doit récupérer la relation
        if (is_object($value)) {
            $method = $this->relationMethod;
            if (method_exists($value, $method)) {
                $relatedEntity = $value->$method();
                if ($relatedEntity) {
                    $getter = 'get' . ucfirst($this->displayAttribute);
                    if (method_exists($relatedEntity, $getter)) {
                        $displayValue = htmlspecialchars((string)$relatedEntity->$getter());
                    } else {
                        $displayValue = htmlspecialchars((string)$relatedEntity->{$this->displayAttribute});
                    }
                } else {
                    $displayValue = '<em>N/A</em>';
                }
            } else {
                $displayValue = '<em>N/A</em>';
            }
        } else {
            $displayValue = htmlspecialchars((string)$value);
        }

        return "<td class=\"crud-table-cell\">{$displayValue}</td>";
    }
}
