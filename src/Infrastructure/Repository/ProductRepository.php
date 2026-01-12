<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Repository\ProductRepositoryInterface;
use App\Domain\Entity\Product;

class ProductRepository implements ProductRepositoryInterface
{
    public function find(int $id): ?Product
    {
        return Product::find($id);
    }

    public function findAll(): array
    {
        return Product::all();
    }

    public function save(Product $product): bool
    {
        return $product->save();
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }
}
