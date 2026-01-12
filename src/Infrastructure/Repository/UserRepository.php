<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Entity\User;

class UserRepository implements UserRepositoryInterface
{
    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function findAll(): array
    {
        return User::all();
    }

    public function findByEmail(string $email): ?User
    {
        return User::findByEmail($email);
    }

    public function save(User $user): bool
    {
        return $user->save();
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
