<?php

declare(strict_types=1);

namespace App\Application\Service\Crud\Input;

abstract class AbstractInput implements InputInterface
{
    protected string $name;
    protected string $label;
    protected mixed $value = null;
    protected bool $required = false;
    protected array $attributes = [];

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

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;
        return $this;
    }

    public function setAttribute(string $key, string $value): self
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    protected function getAttributesString(): string
    {
        $attrs = '';
        foreach ($this->attributes as $key => $value) {
            $attrs .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
        }
        return $attrs;
    }

    protected function validateRequired(mixed $value): array
    {
        if ($this->required && ($value === null || $value === '')) {
            return ["Le champ « {$this->label} » est obligatoire"];
        }
        return [];
    }

    abstract public function render(): string;
}
