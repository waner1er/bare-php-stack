<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Abstract\Model;

class Category extends Model
{
    protected static string $table = 'categories';
    protected static string $primaryKey = 'id';

    public int $id;
    public string $name;
    public string $slug;
    public ?string $description = null;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Récupérer tous les posts de cette catégorie
     */
    public function getPosts(): array
    {
        $db = static::db();
        $stmt = $db->prepare('SELECT * FROM posts WHERE category_id = ? ORDER BY id DESC');
    /**
     * Compter les posts de cette catégorie
     */
    public function getPostCount(): int
    {
        $db = static::db();
        $stmt = $db->prepare('SELECT COUNT(*) as count FROM posts WHERE category_id = ?');
        $stmt->execute([$this->id]);
        $result = $stmt->fetch();

        return (int)$result['count'];
    }
}
