<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity;

use App\Domain\Entity\User;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function test_hydrate_from_array(): void
    {
        $user = new User([
            'id' => 1,
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'email' => 'ada@example.org',
            'password' => 'hashed',
            'role' => 'admin',
        ]);

        $this->assertSame(1, $user->getId());
        $this->assertSame('Ada', $user->getFirstName());
        $this->assertSame('Lovelace', $user->getLastName());
        $this->assertSame('ada@example.org', $user->getEmail());
        $this->assertSame('admin', $user->getRole());
    }

    public function test_unknown_keys_are_ignored(): void
    {
        $user = new User(['email' => 'x@y.z', 'unknown_field' => 'nope']);

        $this->assertSame('x@y.z', $user->getEmail());
        $this->assertFalse(property_exists($user, 'unknown_field'));
    }

    public function test_is_admin(): void
    {
        $admin = new User(['role' => 'admin']);
        $user = new User(['role' => 'user']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($user->isAdmin());
    }

    public function test_default_role_is_user(): void
    {
        $user = new User([]);
        $this->assertSame('user', $user->getRole());
        $this->assertFalse($user->isAdmin());
    }

    public function test_full_name(): void
    {
        $user = new User(['first_name' => 'Grace', 'last_name' => 'Hopper']);
        $this->assertSame('Grace Hopper', $user->getFullName());
    }
}
