<?php

declare(strict_types=1);

namespace App\Controller;

use App\Attribute\Route;
use App\Middleware\AuthMiddleware;

class BackOfficeController extends BaseController
{
    #[Route('/admin', 'GET')]
    public function index(): void
    {
        AuthMiddleware::handle();

        $this->render('backoffice.index');
    }
}
