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

    public function getPosts(): array
    {
        return Post::getByCategory($this->id);
    }

    public function getPostCount(): int
    {
        return count($this->getPosts());
    }
}
