<?php

declare(strict_types=1);

namespace App\Application\Service\Crud\Input;

interface InputInterface
{
    public function getName(): string;
    public function getLabel(): string;
    public function getValue(): mixed;
    public function setValue(mixed $value): self;
    public function render(): string;
    public function isRequired(): bool;
    public function setRequired(bool $required): self;
    public function validate(mixed $value): array;
}
