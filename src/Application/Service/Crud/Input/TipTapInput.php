<?php

declare(strict_types=1);

namespace App\Application\Service\Crud\Input;

use App\Application\Service\Crud\Input\AbstractInput;

class TipTapInput extends AbstractInput
{
    protected int $rows = 10;
    protected ?int $minLength = null;
    protected ?int $maxLength = null;

    public function setRows(int $rows): self
    {
        $this->rows = $rows;
        return $this;
    }

    public function setMinLength(int $min): self
    {
        $this->minLength = $min;
        return $this;
    }

    public function setMaxLength(int $max): self
    {
        $this->maxLength = $max;
        return $this;
    }

    public function validate(mixed $value): array
    {
        $errors = $this->validateRequired($value);

        if ($value === null || $value === '') {
            return $errors;
        }

        if (!is_string($value)) {
            $errors[] = "Le champ « {$this->label} » doit être du texte";
            return $errors;
        }

        $length = mb_strlen(strip_tags($value));

        if ($this->minLength !== null && $length < $this->minLength) {
            $errors[] = "Minimum {$this->minLength} caractères";
        }

        if ($this->maxLength !== null && $length > $this->maxLength) {
            $errors[] = "Maximum {$this->maxLength} caractères";
        }

        return $errors;
    }

    public function render(): string
    {
        $required = $this->required ? 'required' : '';
        $value = htmlspecialchars((string) ($this->value ?? ''));
        $attrs = $this->getAttributesString();

        // Mark this textarea for the admin JS to initialize TipTap
        $wysiwygAttr = 'data-wysiwyg="1"';
        $wysiwygType = 'data-wysiwyg-type="tiptap"';

        return <<<HTML
        <div class="crud-form-group">
            <label for="{$this->name}" class="crud-form-label">
                {$this->label}
                {$this->renderRequiredBadge()}
            </label>
            <textarea
                id="{$this->name}"
                name="{$this->name}"
                rows="{$this->rows}"
                class="crud-form-textarea"
                {$required}
                {$attrs}
                {$wysiwygAttr}
                {$wysiwygType}
            >{$value}</textarea>
        </div>
        HTML;
    }

    protected function renderRequiredBadge(): string
    {
        return $this->required ? '<span class="crud-required">*</span>' : '';
    }
}
