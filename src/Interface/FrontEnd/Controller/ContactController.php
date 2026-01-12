<?php

declare(strict_types=1);

namespace App\Interface\FrontEnd\Controller;

use App\Interface\Common\BaseController;
use App\Interface\Common\Attribute\Route;

class ContactController extends BaseController
{
    #[Route('/contact', 'GET', 'contact')]
    public function index(): void
    {
        $this->render('contact');
    }

    #[Route('/contact', 'POST', 'contact.submit')]
    public function submit(): void
    {
        // TODO: Implémenter l'envoi de message de contact
        // Pour l'instant, on affiche juste un message de succès

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $message = $_POST['message'] ?? '';

        if (empty($name) || empty($email) || empty($message)) {
            $_SESSION['error'] = 'Tous les champs sont requis.';
            header('Location: /contact');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email invalide.';
            header('Location: /contact');
            exit;
        }

        // Simuler l'envoi (à remplacer par un vrai système d'email)
        $_SESSION['success'] = 'Votre message a été envoyé avec succès !';
        header('Location: /contact');
        exit;
    }
}
