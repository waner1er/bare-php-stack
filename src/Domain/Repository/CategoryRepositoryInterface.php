<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Category;

interface CategoryRepositoryInterface
{
    public function find(int $id): ?Category;

    public function findAll(): array;

    public function findBySlug(string $slug): ?Category;

    public function save(Category $category): bool;

    public function delete(Category $category): bool;
}
