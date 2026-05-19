<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity;

use App\Domain\Entity\Category;
use App\Domain\Entity\Post;
use App\Domain\Entity\User;
use PHPUnit\Framework\TestCase;

final class PostTest extends TestCase
{
    public function test_hydrate_casts_is_in_menu_to_bool(): void
    {
        $post = new Post([
            'id' => 1,
            'title' => 'Hello',
            'slug' => 'hello',
            'content' => 'World',
            'user_id' => 1,
            'is_in_menu' => 1,
            'menu_order' => 3,
        ]);

        $this->assertTrue($post->getIsInMenu());
        $this->assertSame(3, $post->getMenuOrder());
    }

    public function test_relations_are_null_by_default(): void
    {
        $post = new Post(['id' => 1, 'user_id' => 1]);
        $this->assertNull($post->getUser());
        $this->assertNull($post->getCategory());
    }

    public function test_relations_are_set_by_repository(): void
    {
        $post = new Post(['id' => 1, 'user_id' => 1, 'category_id' => 2]);
        $user = new User(['id' => 1, 'first_name' => 'A', 'last_name' => 'B']);
        $category = new Category(['id' => 2, 'name' => 'News', 'slug' => 'news']);

        $post->setUser($user);
        $post->setCategory($category);

        $this->assertSame($user, $post->getUser());
        $this->assertSame($category, $post->getCategory());
    }

    public function test_category_can_be_null(): void
    {
        $post = new Post(['id' => 1, 'user_id' => 1]);
        $this->assertNull($post->getCategoryId());
    }
}
