<?php

declare(strict_types=1);

namespace App\Application\Service\Crud\Input;

use App\Application\Service\Crud\Input\AbstractInput;

class SelectInput extends AbstractInput
{
    protected array $options = [];


    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    public function validate(mixed $value): array
    {
        $errors = $this->validateRequired($value);

        if ($value === null || $value === '') {
            return $errors;
        }

        if (!array_key_exists($value, $this->options)) {
            $errors[] = "Valeur invalide pour « {$this->label} »";
        }

        return $errors;
    }


    public function render(): string
    {
        $required = $this->required ? 'required' : '';
        $attrs = $this->getAttributesString();

        $optionsHtml = '';

        foreach ($this->options as $key => $label) {
            $selected = ((string) $key === (string) $this->value) ? 'selected' : '';
            $keyEscaped = htmlspecialchars((string) $key);
            $labelEscaped = htmlspecialchars((string) $label);

            $optionsHtml .= "<option value=\"{$keyEscaped}\" {$selected}>{$labelEscaped}</option>\n";
        }

        return <<<HTML
        <div class="crud-form-group">
            <label for="{$this->name}" class="crud-form-label">
                {$this->label}
                {$this->renderRequiredBadge()}
            </label>
            <select 
                id="{$this->name}" 
                name="{$this->name}" 
                class="crud-form-select"
                {$required}
                {$attrs}
            >
                {$optionsHtml}
            </select>
        </div>
        HTML;
    }

    protected function renderRequiredBadge(): string
    {
        return $this->required ? '<span class="crud-required">*</span>' : '';
    }
}
