<?php

declare(strict_types=1);

namespace App\Interface\FrontEnd\Controller;

use App\Domain\Entity\Post;
use App\Domain\Entity\User;
use App\Interface\Common\BaseController;
use App\Interface\Common\Attribute\Route;

class PostController extends BaseController
{

    #[Route('/posts', 'GET', 'posts.index')]
    public function index(): void
    {
        $posts = Post::all();
        $this->render('posts.index', ['posts' => $posts]);
    }

    #[Route('/posts/{slug}', 'GET', 'posts.show')]
    public function show(string $slug): void
    {
        $post = Post::findBySlug($slug);

        if (!$post) {
            http_response_code(404);
            echo "Post non trouvé.";
            return;
        }

        $this->render('posts.show', ['post' => $post]);
    }
}
