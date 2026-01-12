<?php

declare(strict_types=1);

namespace App\Interface\FrontEnd\Controller;

use App\Interface\Common\BaseController;
use App\Interface\Common\Attribute\Route;
use App\Domain\Repository\PostRepositoryInterface;
use App\Infrastructure\Repository\PostRepository;

class HomeController extends BaseController
{
    public function __construct(private PostRepositoryInterface $postRepository = new PostRepository()) {}

    #[Route('/', 'GET', 'home')]
    public function index(): void
    {

        $this->render('home', [
            'recentPosts' => $this->postRepository->findAll(),
        ]);
    }
}
