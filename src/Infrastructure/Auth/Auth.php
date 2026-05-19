<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use App\Infrastructure\Repository\UserRepository;
use App\Infrastructure\Session\Session;

class Auth
{
    private static ?UserRepositoryInterface $userRepository = null;

    public static function setUserRepository(UserRepositoryInterface $repository): void
    {
        self::$userRepository = $repository;
    }

    private static function users(): UserRepositoryInterface
    {
        return self::$userRepository ??= new UserRepository();
    }

    public static function attempt(string $email, string $password): bool
    {
        $user = self::users()->findByEmail($email);

        if (!$user || !password_verify($password, $user->password)) {
            return false;
        }

        Session::set('user_id', $user->getId());
        Session::set('user_email', $user->getEmail());
        Session::regenerate();

        return true;
    }

    public static function user(): ?User
    {
        if (!self::check()) {
            return null;
        }

        $userId = Session::get('user_id');
        return self::users()->find((int) $userId);
    }

    public static function check(): bool
    {
        return Session::has('user_id');
    }

    public static function logout(): void
    {
        Session::remove('user_id');
        Session::remove('user_email');
    }

    public static function id(): ?int
    {
        return Session::get('user_id');
    }

    public static function register(string $firstName, string $lastName, string $email, string $password): bool
    {
        $users = self::users();

        if ($users->findByEmail($email)) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $user = new User([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => 'user',
        ]);

        $result = $users->save($user);

        if ($result) {
            Session::set('user_id', $user->getId());
            Session::set('user_email', $user->getEmail());
            Session::regenerate();
        }

        return $result;
    }
}
