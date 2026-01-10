<?php

declare(strict_types=1);

namespace App\Domain\Interface;

interface PostInterface
{
    public function getId(): int;
    public function getTitle(): string;
    public function getSlug(): string;
    public function getContent(): string;
    public function getUserId(): int;
}
