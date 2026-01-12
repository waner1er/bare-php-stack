<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Test;

interface TestRepositoryInterface
{
    public function find(int $id): ?Test;

    public function findAll(): array;

    public function save(Test $test): bool;

    public function delete(Test $test): bool;
}
