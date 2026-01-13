<?php

declare(strict_types=1);

namespace App\Application\Service\Crud\Input;

use App\Application\Service\Crud\Input\AbstractInput;

class NumberInput extends AbstractInput
{
    protected ?float $min = null;
    protected ?float $max = null;


    public function setMin(float $min): self
    {
        $this->min = $min;
        $this->setAttribute('min', (string)$min);
        return $this;
    }

    public function setMax(float $max): self
    {
        $this->max = $max;
        $this->setAttribute('max', (string)$max);
        return $this;
    }


    public function validate(mixed $value): array
    {
        $errors = $this->validateRequired($value);

        if ($value === null || $value === '') {
            return $errors;
        }

        if (!is_numeric($value)) {
            $errors[] = "Le champ « {$this->label} » doit être un nombre";
            return $errors;
        }

        $numericValue = (float) $value;

        if ($this->min !== null && $numericValue < $this->min) {
            $errors[] = "La valeur minimale pour « {$this->label} » est {$this->min}";
        }

        if ($this->max !== null && $numericValue > $this->max) {
            $errors[] = "La valeur maximale pour « {$this->label} » est {$this->max}";
        }

        return $errors;
    }


    public function render(): string
    {
        $required = $this->required ? 'required' : '';
        $value = htmlspecialchars((string)($this->value ?? ''));
        $attrs = $this->getAttributesString();

        return <<<HTML
        <div class="crud-form-group">
            <label for="{$this->name}" class="crud-form-label">
                {$this->label}
                {$this->renderRequiredBadge()}
            </label>
            <input 
                type="number" 
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
