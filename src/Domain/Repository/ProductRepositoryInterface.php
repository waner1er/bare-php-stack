<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    public function find(int $id): ?Product;

    public function findAll(): array;

    public function save(Product $product): bool;

    public function delete(Product $product): bool;
}
