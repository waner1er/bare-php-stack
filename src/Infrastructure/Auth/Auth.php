<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Infrastructure\Database\Database;
use App\Infrastructure\Session\Session;
use App\Domain\Entity\User;

class Auth
{
    public static function attempt(string $email, string $password): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $userData = $stmt->fetch();

        if (!$userData) {
            return false;
        }

        if (!password_verify($password, $userData['password'])) {
            return false;
        }

        Session::set('user_id', $userData['id']);
        Session::set('user_email', $userData['email']);

        Session::regenerate();

        return true;
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        $userId = Session::get('user_id');
        return User::find($userId);
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
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)');
        $result = $stmt->execute([$firstName, $lastName, $email, $hashedPassword]);

        if ($result) {
            $userId = $pdo->lastInsertId();
            Session::set('user_id', $userId);
            Session::set('user_email', $email);

            Session::regenerate();
        }

        return $result;
    }
}
