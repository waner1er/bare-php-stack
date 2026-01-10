<?php

declare(strict_types=1);

namespace App\Controller;

use App\Tools\Auth;
use App\Tools\Session;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Attribute\Route;

class AuthController extends BaseController
{
    #[Route('/login', 'GET')]
    public function showLogin(): void
    {
        AuthMiddleware::guest();

        $this->render('auth.login', [
            'error' => Session::getFlash('error'),
        ]);
    }

    #[Route('/login', 'POST')]
    public function login(): void
    {
        AuthMiddleware::guest();
        CsrfMiddleware::handle();

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            Session::flash('error', 'Email et mot de passe requis');
            header('Location: /login');
            exit;
        }

        if (Auth::attempt($email, $password)) {
            // Redirige vers l'URL prévue ou la page d'accueil
            $redirectTo = AuthMiddleware::getIntendedUrl('/');
            header('Location: ' . $redirectTo);
            exit;
        }

        Session::flash('error', 'Email ou mot de passe incorrect');
        header('Location: /login');
    }

    #[Route('/register', 'GET')]
    public function showRegister(): void
    {
        AuthMiddleware::guest();

        $this->render('auth.register', [
            'error' => Session::getFlash('error'),
        ]);
    }

    #[Route('/register', 'POST')]
    public function register(): void
    {
        AuthMiddleware::guest();
        CsrfMiddleware::handle();

        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['password_confirmation'] ?? '';

        if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
            Session::flash('error', 'Tous les champs sont requis');
            header('Location: /register');
            exit;
        }

        if ($password !== $confirmPassword) {
            Session::flash('error', 'Les mots de passe ne correspondent pas');
            header('Location: /register');
            exit;
        }

        if (strlen($password) < 6) {
            Session::flash('error', 'Le mot de passe doit contenir au moins 6 caractères');
            header('Location: /register');
            exit;
        }

        if (Auth::register($firstName, $lastName, $email, $password)) {
            header('Location: /');
            exit;
        }

        Session::flash('error', 'Cet email est déjà utilisé');
        header('Location: /register');
    }

    #[Route('/logout', 'GET')]
    public function logout(): void
    {
        AuthMiddleware::handle();

        Auth::logout();
        header('Location: /login');
    }
}
