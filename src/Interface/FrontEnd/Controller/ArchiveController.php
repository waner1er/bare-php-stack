<?php

declare(strict_types=1);

namespace App\Interface\FrontEnd\Controller;

use App\Interface\Common\BaseController;
use App\Interface\Common\Attribute\Route;
use App\Domain\Entity\Post;
use App\Domain\Entity\Category;

class ArchiveController extends BaseController
{
    #[Route('/archive', 'GET', 'archive')]
    public function index(): void
    {
        $categorySlug = $_GET['category'] ?? null;

        if ($categorySlug) {
            // Filtrer par catégorie
            $category = Category::findBySlug($categorySlug);

            if (!$category) {
                // Catégorie non trouvée
                header('Location: /archive');
                exit;
            }

            $posts = Post::getByCategory($category->getId());
        } else {
            // Tous les posts
            $posts = Post::all();
        }

        // Récupérer toutes les catégories pour le filtre
        $categories = Category::all();

        $this->render('archive', [
            'posts' => $posts,
            'categories' => $categories,
            'currentCategory' => $categorySlug ? $category : null
        ]);
    }
}
