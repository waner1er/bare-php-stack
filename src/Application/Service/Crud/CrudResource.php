<?php

declare(strict_types=1);

namespace App\Application\Service\Crud;

use App\Application\Service\Crud\Input\InputInterface;
use App\Application\Service\Crud\Column\ColumnInterface;

abstract class CrudResource
{
    protected string $entityClass;
    protected string $title;
    protected string $singularTitle;

    /**
     * Définir les colonnes pour la vue en liste
     * @return ColumnInterface[]
     */
    abstract public function columns(): array;

    /**
     * Définir les champs du formulaire
     * @return InputInterface[]
     */
    abstract public function inputs(): array;

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSingularTitle(): string
    {
        return $this->singularTitle;
    }

    public function validate(array $data): array
    {
        $errors = [];

        foreach ($this->inputs() as $input) {
            $name = $input->getName();
            $value = $data[$name] ?? null;

            $fieldErrors = $input->validate($value);

            if (!empty($fieldErrors)) {
                $errors[$name] = $fieldErrors;
            }
        }

        return $errors;
    }

    /**
     * Génère le HTML du tableau de liste
     */
    public function renderTable(array $entities): string
    {
        $columns = $this->columns();

        $html = '<div class="crud-table-wrapper">';
        $html .= '<table class="crud-table">';
        $html .= '<thead><tr>';

        /** @var ColumnInterface $column */
        foreach ($columns as $column) {
            $html .= $column->renderHeader();
        }
        $html .= '<th class="crud-table-header crud-table-actions">Actions</th>';
        $html .= '</tr></thead>';
        $html .= '<tbody>';

        foreach ($entities as $entity) {
            $html .= '<tr>';
            /** @var ColumnInterface $column */
            foreach ($columns as $column) {
                // Pour les RelationColumn, passer l'entité complète
                if ($column instanceof \App\Application\Service\Crud\Column\RelationColumn) {
                    $html .= $column->renderCell($entity);
                } else {
                    // Pour les autres colonnes, récupérer la valeur de la propriété
                    $property = $column->getName();
                    $getter = 'get' . ucfirst($property);
                    if (method_exists($entity, $getter)) {
                        $value = $entity->$getter();
                    } else {
                        $value = $entity->$property ?? null;
                    }
                    $html .= $column->renderCell($value);
                }
            }

            $getter = method_exists($entity, 'getId') ? 'getId' : null;
            $id = $getter ? $entity->$getter() : ($entity->id ?? null);
            $html .= '<td class="crud-table-actions">';
            $html .= "<a href=\"?action=edit&id={$id}\" class=\"crud-btn crud-btn-edit\">Éditer</a>";
            $html .= "<a href=\"?action=delete&id={$id}\" class=\"crud-btn crud-btn-delete\" onclick=\"return confirm('Confirmer la suppression ?')\">Supprimer</a>";
            $html .= '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Génère le HTML du formulaire (sans balises form, géré par la vue)
     */
    public function renderForm(?object $entity = null): string
    {
        $inputs = $this->inputs();
        $leftHtml = '';
        $rightHtml = '';

        /** @var InputInterface $input */
        foreach ($inputs as $input) {
            if ($entity) {
                $property = $input->getName();
                $getter = 'get' . ucfirst($property);
                if (method_exists($entity, $getter)) {
                    $value = $entity->$getter();
                } else {
                    $value = $entity->$property ?? null;
                }
                $input->setValue($value);
            }

            // Place WYSIWYG (TextareaInput with wysiwyg enabled) in the right column
            $isWysiwyg = false;
            if ($input instanceof \App\Application\Service\Crud\Input\TextareaInput) {
                $isWysiwyg = method_exists($input, 'isWysiwyg') && $input->isWysiwyg();
            }

            if ($isWysiwyg) {
                $rightHtml .= $input->render();
            } else {
                $leftHtml .= $input->render();
            }
        }

        $html = '';
        $html .= '<div class="crud-form-grid">';
        $html .= '<div class="crud-form-grid__left">' . $leftHtml . '</div>';
        $html .= '<div class="crud-form-grid__right">' . $rightHtml . '</div>';
        $html .= '</div>';

        $html .= '<div class="crud-form-actions">';
        $html .= '<button type="submit" class="crud-btn crud-btn-primary">Enregistrer</button>';
        $html .= '<a href="?" class="crud-btn crud-btn-secondary">Annuler</a>';
        $html .= '</div>';

        return $html;
    }
}
