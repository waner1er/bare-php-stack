<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Repository\TestRepositoryInterface;
use App\Domain\Entity\Test;

class TestRepository implements TestRepositoryInterface
{
    public function find(int $id): ?Test
    {
        return Test::find($id);
    }

    public function findAll(): array
    {
        return Test::all();
    }

    public function save(Test $test): bool
    {
        return $test->save();
    }

    public function delete(Test $test): bool
    {
        return $test->delete();
    }
}
