<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Category;
use App\Domain\Repository\CategoryRepositoryInterface;
use App\Infrastructure\Persistence\AbstractRepository;

class CategoryRepository extends AbstractRepository implements CategoryRepositoryInterface
{
    protected string $table = 'categories';
    protected string $entityClass = Category::class;

    public function find(int $id): ?Category
    {
        /** @var Category|null */
        return $this->findOneRaw($id);
    }

    /** @return Category[] */
    public function findAll(): array
    {
        /** @var Category[] */
        return $this->findAllRaw();
    }

    public function findBySlug(string $slug): ?Category
    {
        /** @var Category|null */
        return $this->findOneBy('slug', $slug);
    }

    public function save(Category $category): bool
    {
        return $this->persist($category);
    }

    public function delete(Category $category): bool
    {
        return $this->remove($category);
    }
}
