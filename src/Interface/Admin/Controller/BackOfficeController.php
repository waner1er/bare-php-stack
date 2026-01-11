<?php

declare(strict_types=1);

namespace App\Interface\Admin\Controller;

use App\Interface\Common\Attribute\Route;
use App\Interface\Common\BaseController;
use App\Infrastructure\Middleware\AdminMiddleware;

class BackOfficeController extends BaseController
{
    #[Route('/admin', 'GET')]
    public function index(): void
    {
        AdminMiddleware::handle();

        $this->render('backoffice.index');
    }
}
