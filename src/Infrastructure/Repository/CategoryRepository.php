<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Repository\CategoryRepositoryInterface;
use App\Domain\Entity\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function find(int $id): ?Category
    {
        return Category::find($id);
    }

    public function findAll(): array
    {
        return Category::all();
    }

    public function findBySlug(string $slug): ?Category
    {
        return Category::findBySlug($slug);
    }

    public function save(Category $category): bool
    {
        return $category->save();
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }
}
