<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Abstract\Model;
use App\Domain\Contract\SlugResourceInterface;

class Post extends Model implements SlugResourceInterface
{
    /**
     * Retourne tous les slugs de posts à afficher dans le menu
     * @return array<int, array{slug: string, title: string, type: string}>
     */
    public static function getAllSlugsForMenu(): array
    {
        return array_map(
            fn($post) => [
                'slug' => $post->getSlug(),
                'title' => $post->getTitle(),
                'type' => 'post',
            ],
            array_filter(static::all(), fn($post) => $post->getIsInMenu()),
        );
    }
    protected static string $table = 'posts';
    protected static string $primaryKey = 'id';

    public int $id;
    public string $title;
    public string $slug;
    public string $content;
    public int $user_id;
    public ?int $category_id = null;
    public bool $is_in_menu = false;
    public int $menu_order = 0;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                if ($key === 'is_in_menu') {
                    $this->$key = (bool) $value;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function user(): ?User
    {
        return User::find($this->user_id);
    }

    public function getIsInMenu(): bool
    {
        return $this->is_in_menu;
    }

    public function setIsInMenu(bool $is_in_menu): void
    {
        $this->is_in_menu = $is_in_menu;
    }

    public function getMenuOrder(): int
    {
        return $this->menu_order;
    }

    public function setMenuOrder(int $menu_order): void
    {
        $this->menu_order = $menu_order;
    }

    public function getCategoryId(): ?int
    {
        return $this->category_id;
    }

    public function setCategoryId(?int $category_id): void
    {
        $this->category_id = $category_id;
    }

    public function getCategory(): ?Category
    {
        if ($this->category_id === null) {
            return null;
        }
        return Category::find($this->category_id);
    }

    public static function getMenuItems(): array
    {
        $posts = static::all();
        $menuPosts = array_filter($posts, fn($post) => $post->getIsInMenu());
        usort($menuPosts, fn($a, $b) => $a->getMenuOrder() <=> $b->getMenuOrder());
        return $menuPosts;
    }


    public static function getByCategory(int $categoryId): array
    {
        $posts = static::all();
        $filtered = array_filter($posts, fn($post) => $post->getCategoryId() === $categoryId);
        usort($filtered, fn($a, $b) => $b->getId() <=> $a->getId());
        return $filtered;
    }
}
