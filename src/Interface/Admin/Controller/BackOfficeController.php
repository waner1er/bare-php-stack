<?php

declare(strict_types=1);

namespace App\Interface\Admin\Controller;

use Illuminate\Support\Facades\Route;
use App\Interface\Common\BaseController;
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
