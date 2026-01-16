<?php

declare(strict_types=1);

namespace App\Application\Service\Crud\Input;

class TextInput extends AbstractInput
{
    public function validate(mixed $value): array
    {
        $errors = $this->validateRequired($value);

        if ($value === null || $value === '') {
            return $errors;
        }

        if (!is_string($value)) {
            $errors[] = "Le champ « {$this->label} » doit être une chaîne de caractères";
        }

        return $errors;
    }

    public function render(): string
    {
        $required = $this->required ? 'required' : '';
        $value = htmlspecialchars((string) $this->value);
        $attrs = $this->getAttributesString();

        return <<<HTML
        <div class="crud-form-group">
            <label for="{$this->name}" class="crud-form-label">
                {$this->label}
                {$this->renderRequiredBadge()}
            </label>
            <input 
                type="text" 
                id="{$this->name}" 
                name="{$this->name}" 
                value="{$value}" 
                class="crud-form-input"
                {$required}
                {$attrs}
            />
        </div>
        HTML;
    }

    protected function renderRequiredBadge(): string
    {
        return $this->required ? '<span class="crud-required">*</span>' : '';
    }
}
