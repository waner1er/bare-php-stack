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
        $postsData = Post::all();
        $posts = array_map(fn($row) => new Post($row), $postsData);
        $this->render('posts/index', ['posts' => $posts]);
    }

    #[Route('/posts/{id}', 'GET', 'posts.show')]
    public function show(int $id): void
    {
        $row = Post::find($id);
        if ($row) {
            $post = new Post($row);
            $this->render('posts/show', ['post' => $post]);
        } else {
            http_response_code(404);
            echo "Post not found.";
        }
    }

    #[Route('/', 'GET', 'home')]
    public function home(): void
    {
        $users = User::all();
        $this->render('home', ['users' => $users]);
    }
}
