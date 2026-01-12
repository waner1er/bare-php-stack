<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Repository\MenuItemRepositoryInterface;
use App\Domain\Entity\MenuItem;

class MenuItemRepository implements MenuItemRepositoryInterface
{
    public function find(int $id): ?MenuItem
    {
        return MenuItem::find($id);
    }

    public function findAll(): array
    {
        return MenuItem::all();
    }

    public function findVisible(): array
    {
        return MenuItem::getVisibleItems();
    }

    public function findByPosition(int $position): ?MenuItem
    {
        $items = array_filter(MenuItem::all(), fn($item) => $item->getPosition() === $position);
        return !empty($items) ? reset($items) : null;
    }

    public function save(MenuItem $menuItem): bool
    {
        return $menuItem->save();
    }

    public function delete(MenuItem $menuItem): bool
    {
        return $menuItem->delete();
    }
}
