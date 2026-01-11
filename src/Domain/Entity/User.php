<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Abstract\Model;

class User extends Model
{
    protected static string $table = 'users';
    protected static string $primaryKey = 'id';

    public int $id;
    public string $first_name;
    public string $last_name;
    public string $email;
    public string $password;
    public string $role = 'user';

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): void
    {
        $this->first_name = $first_name;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): void
    {
        $this->last_name = $last_name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function getFullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
