<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\MenuItem;
use App\Domain\Repository\MenuItemRepositoryInterface;
use App\Infrastructure\Persistence\AbstractRepository;

class MenuItemRepository extends AbstractRepository implements MenuItemRepositoryInterface
{
    protected string $table = 'menuitems';
    protected string $entityClass = MenuItem::class;

    public function find(int $id): ?MenuItem
    {
        /** @var MenuItem|null */
        return $this->findOneRaw($id);
    }

    /** @return MenuItem[] */
    public function findAll(): array
    {
        /** @var MenuItem[] */
        return $this->findAllRaw();
    }

    /** @return MenuItem[] */
    public function findVisible(): array
    {
        $stmt = $this->db()->prepare(
            'SELECT * FROM ' . $this->table . ' WHERE is_visible = 1 ORDER BY position ASC'
        );
        $stmt->execute();
        return array_map(fn($row) => $this->hydrate($row), $stmt->fetchAll());
    }

    public function findByPosition(int $position): ?MenuItem
    {
        /** @var MenuItem|null */
        return $this->findOneBy('position', $position);
    }

    public function save(MenuItem $menuItem): bool
    {
        return $this->persist($menuItem);
    }

    public function delete(MenuItem $menuItem): bool
    {
        return $this->remove($menuItem);
    }
}
