<?php

declare(strict_types=1);

namespace App\Interface\FrontEnd\Controller;

use App\Domain\Repository\CategoryRepositoryInterface;
use App\Domain\Repository\PostRepositoryInterface;
use App\Infrastructure\Repository\CategoryRepository;
use App\Infrastructure\Repository\PostRepository;
use App\Interface\Common\Attribute\Route;
use App\Interface\Common\BaseController;

class PostController extends BaseController
{
    public function __construct(
        private PostRepositoryInterface $postRepository = new PostRepository(),
        private CategoryRepositoryInterface $categoryRepository = new CategoryRepository(),
    ) {}

    #[Route('/posts', 'GET', 'posts.index')]
    public function index(): void
    {
        $this->render('posts.index', [
            'posts' => $this->postRepository->findAll(),
            'categories' => $this->categoryRepository->findAll(),
            'postCounts' => $this->postRepository->countByCategory(),
            'currentCategory' => null,
        ]);
    }

    #[Route('/archive', 'GET', 'archive')]
    public function archive(): void
    {
        $categorySlug = $_GET['category'] ?? null;

        if ($categorySlug) {
            $category = $this->categoryRepository->findBySlug($categorySlug);
            if (!$category) {
                header('Location: /archive');
                exit;
            }
            $items = $this->postRepository->findByCategory($category->getId());
        } else {
            $items = $this->postRepository->findAll();
        }

        $categories = $this->categoryRepository->findAll();
        $postCounts = $this->postRepository->countByCategory();

        $this->render('archive', [
            'items' => $items,
            'posts' => $items,
            'categories' => $categories,
            'postCounts' => $postCounts,
            'currentCategory' => $categorySlug ? $category : null,
            'entityLabel' => 'articles',
        ]);
    }

    #[Route('/posts/{slug}', 'GET', 'posts.show')]
    public function show(string $slug): void
    {
        $category = $this->categoryRepository->findBySlug($slug);

        if ($category) {
            $posts = $this->postRepository->findByCategory($category->getId());
            $categories = $this->categoryRepository->findAll();
            $postCounts = $this->postRepository->countByCategory();

            $this->render('archive', [
                'posts' => $posts,
                'categories' => $categories,
                'postCounts' => $postCounts,
                'currentCategory' => $category,
            ]);
            return;
        }

        $post = $this->postRepository->findBySlug($slug);

        if (!$post) {
            http_response_code(404);
            echo "Post non trouvé.";
            return;
        }

        $this->render('posts.show', ['post' => $post]);
    }

    #[Route('/{slug}', 'GET', 'page.show')]
    public function showPage(string $slug): void
    {
        $post = $this->postRepository->findBySlug($slug);

        if ($post && $post->getIsInMenu()) {
            $this->render('posts.show', ['post' => $post]);
            return;
        }

        http_response_code(404);
        echo "Page non trouvée.";
    }
}
