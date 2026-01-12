<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Abstract\Model;

class MenuItem extends Model
{
    protected static string $table = 'menuitems';
    protected static string $primaryKey = 'id';

    public int $id;
    public string $label;
    public string $slug;
    public string $type;
    public string $entity_type = 'Post';
    public int $position;
    public bool $is_visible;
    public ?int $category_id = null;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                if ($key === 'is_visible') {
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

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getIsVisible(): bool
    {
        return $this->is_visible;
    }

    public function setIsVisible(bool $is_visible): void
    {
        $this->is_visible = $is_visible;
    }

    public function getEntityType(): string
    {
        return $this->entity_type;
    }

    public function setEntityType(string $entity_type): void
    {
        $this->entity_type = $entity_type;
    }

    public function getCategoryId(): ?int
    {
        return $this->category_id;
    }

    public function setCategoryId(?int $category_id): void
    {
        $this->category_id = $category_id;
    }

    public static function getVisibleItems(): array
    {
        $items = static::all();
        $visibleItems = array_filter($items, fn($item) => $item->getIsVisible());
        usort($visibleItems, fn($a, $b) => $a->getPosition() <=> $b->getPosition());
        return $visibleItems;
    }
}
