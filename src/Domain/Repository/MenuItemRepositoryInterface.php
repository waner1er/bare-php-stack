<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\MenuItem;

interface MenuItemRepositoryInterface
{
    public function find(int $id): ?MenuItem;

    public function findAll(): array;

    public function findVisible(): array;

    public function findByPosition(int $position): ?MenuItem;

    public function save(MenuItem $menuItem): bool;

    public function delete(MenuItem $menuItem): bool;
}
