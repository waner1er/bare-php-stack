<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function find(int $id): ?User;

    public function findAll(): array;

    public function findByEmail(string $email): ?User;

    public function save(User $user): bool;

    public function delete(User $user): bool;
}
