<?php

declare(strict_types=1);

namespace App\Interface\FrontEnd\Controller;

use App\Interface\Common\BaseController;
use App\Interface\Common\Attribute\Route;
use App\Domain\Entity\Post;

class HomeController extends BaseController
{
    #[Route('/', 'GET', 'home')]
    public function index(): void
    {
        // Récupérer les derniers posts pour la page d'accueil
        $db = Post::db();
        $stmt = $db->query('SELECT * FROM posts WHERE category_id IS NOT NULL ORDER BY id DESC LIMIT 6');
        $results = $stmt->fetchAll();
        $recentPosts = array_map(fn($row) => new Post($row), $results);

        $this->render('home', [
            'recentPosts' => $recentPosts
        ]);
    }
}
