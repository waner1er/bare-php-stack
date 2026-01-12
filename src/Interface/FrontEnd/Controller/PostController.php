<?php

declare(strict_types=1);

namespace App\Interface\FrontEnd\Controller;

use App\Domain\Repository\PostRepositoryInterface;
use App\Domain\Repository\CategoryRepositoryInterface;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Infrastructure\Repository\PostRepository;
use App\Infrastructure\Repository\CategoryRepository;
use App\Infrastructure\Repository\ProductRepository;
use App\Interface\Common\BaseController;
use App\Interface\Common\Attribute\Route;

class PostController extends BaseController
{
    public function __construct(
        private PostRepositoryInterface $postRepository = new PostRepository(),
        private CategoryRepositoryInterface $categoryRepository = new CategoryRepository(),
        private ProductRepositoryInterface $productRepository = new ProductRepository(),
    ) {}

    #[Route('/posts', 'GET', 'posts.index')]
    public function index(): void
    {
        $posts = $this->postRepository->findAll();
        $this->render('posts.index', ['posts' => $posts]);
    }

    #[Route('/archive', 'GET', 'archive')]
    public function archive(): void
    {
        $categorySlug = $_GET['category'] ?? null;
        $entityType = $_GET['entity'] ?? 'Post';

        // Déterminer le repository et le modèle selon le type d'entité
        $repository = match ($entityType) {
            'Post' => $this->postRepository,
            'Product' => $this->productRepository,
            // 'Event' => $this->eventRepository,     // À ajouter quand Event existe
            default => $this->postRepository,
        };

        if ($categorySlug) {
            $category = $this->categoryRepository->findBySlug($categorySlug);

            if (!$category) {
                header('Location: /archive?entity=' . $entityType);
                exit;
            }

            $items = $repository->findByCategory($category->getId());
        } else {
            $items = $repository->findAll();
        }

        $categories = $this->categoryRepository->findAll();

        // Déterminer la vue selon le type d'entité
        $viewName = match ($entityType) {
            'Post' => 'archive',
            // 'Product' => 'products.archive',
            // 'Event' => 'events.archive',
            default => 'archive',
        };

        $this->render($viewName, [
            'items' => $items,
            'posts' => $items, // Pour la compatibilité avec archive.blade.php
            'categories' => $categories,
            'currentCategory' => $categorySlug ? $category : null,
            'entityType' => $entityType,
            'entityLabel' => match ($entityType) {
                'Post' => 'articles',
                'Product' => 'produits',
                'Event' => 'événements',
                default => 'articles',
            },
        ]);
    }

    #[Route('/posts/{slug}', 'GET', 'posts.show')]
    public function show(string $slug): void
    {
        // Vérifier d'abord si c'est un slug de catégorie
        $category = $this->categoryRepository->findBySlug($slug);

        if ($category) {
            // C'est une catégorie, afficher la liste des posts de cette catégorie
            $posts = $this->postRepository->findByCategory($category->getId());
            $categories = $this->categoryRepository->findAll();

            $this->render('archive', [
                'posts' => $posts,
                'categories' => $categories,
                'currentCategory' => $category,
            ]);
            return;
        }

        // Sinon, chercher un post avec ce slug
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
        // Chercher un post avec ce slug qui est dans le menu
        $post = $this->postRepository->findBySlug($slug);

        if ($post && $post->getIsInMenu()) {
            $this->render('posts.show', ['post' => $post]);
            return;
        }

        // Si pas trouvé ou pas dans le menu, 404
        http_response_code(404);
        echo "Page non trouvée.";
    }
}
