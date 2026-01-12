<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Repository\PostRepositoryInterface;
use App\Domain\Entity\Post;

class PostRepository implements PostRepositoryInterface
{
    public function find(int $id): ?Post
    {
        return Post::find($id);
    }

    public function findAll(): array
    {
        return Post::all();
    }

    public function findBySlug(string $slug): ?Post
    {
        return Post::findBySlug($slug);
    }

    public function findByCategory(int $categoryId): array
    {
        return Post::getByCategory($categoryId);
    }

    public function findMenuItems(): array
    {
        return Post::getMenuItems();
    }

    public function save(Post $post): bool
    {
        return $post->save();
    }

    public function delete(Post $post): bool
    {
        return $post->delete();
    }
}
