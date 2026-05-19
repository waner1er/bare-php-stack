<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use App\Infrastructure\Persistence\AbstractRepository;

class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
    protected string $table = 'users';
    protected string $entityClass = User::class;

    public function find(int $id): ?User
    {
        /** @var User|null */
        return $this->findOneRaw($id);
    }

    /** @return User[] */
    public function findAll(): array
    {
        /** @var User[] */
        return $this->findAllRaw();
    }

    public function findByEmail(string $email): ?User
    {
        /** @var User|null */
        return $this->findOneBy('email', $email);
    }

    public function save(User $user): bool
    {
        return $this->persist($user);
    }

    public function delete(User $user): bool
    {
        return $this->remove($user);
    }
}
