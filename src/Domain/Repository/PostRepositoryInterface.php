<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Post;

interface PostRepositoryInterface
{
    public function find(int $id): ?Post;

    public function findAll(): array;

    public function findBySlug(string $slug): ?Post;

    public function findByCategory(int $categoryId): array;

    public function findMenuItems(): array;

    public function save(Post $post): bool;

    public function delete(Post $post): bool;
}
