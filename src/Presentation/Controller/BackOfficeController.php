<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Presentation\Attribute\Route;
use App\Infrastructure\Middleware\AuthMiddleware;

class BackOfficeController extends BaseController
{
    #[Route('/admin', 'GET')]
    public function index(): void
    {
        AuthMiddleware::handle();

        $this->render('backoffice.index');
    }
}
