<?php

declare(strict_types=1);

namespace App\Interface\Admin\Controller;

use App\Domain\Entity\Post;
use App\Interface\Common\Attribute\Route;
use App\Interface\Common\BaseController;
use App\Infrastructure\Middleware\AdminMiddleware;
use App\Infrastructure\Middleware\CsrfMiddleware;
use App\Infrastructure\Session\Session;

class PostAdminController extends BaseController
{
    #[Route('/admin/posts', 'GET')]
    public function index(): void
    {
        AdminMiddleware::handle();

        $posts = Post::all();

        // Réorganiser automatiquement les ordres pour éviter les doublons
        $this->reorderMenuItems();

        // Récupérer les posts (tous les posts, triés par menu d'abord)
        $db = Post::db();
        $stmt = $db->query('SELECT * FROM posts ORDER BY is_in_menu DESC, menu_order ASC, title ASC');
        $results = $stmt->fetchAll();
        $posts = array_map(fn($row) => new Post($row), $results);

        // Créer un index de position pour les posts dans le menu
        $menuPositions = [];
        $position = 1;
        foreach ($posts as $post) {
            if ($post->getIsInMenu()) {
                $menuPositions[$post->getId()] = $position++;
            }
        }

        $this->render('posts.index', [
            'posts' => $posts,
            'menuPositions' => $menuPositions
        ]);
    }

    #[Route('/admin/posts/{id}/toggle-menu', 'POST')]
    public function toggleMenu(int $id): void
    {
        AdminMiddleware::handle();
        CsrfMiddleware::handle();

        $post = Post::find($id);

        if (!$post) {
            Session::flash('error', 'Article introuvable');
            header('Location: /admin/posts');
            exit;
        }

        $isInMenu = $post->getIsInMenu();
        $post->setIsInMenu(!$isInMenu);

        // Si on ajoute au menu, mettre à la fin
        if (!$isInMenu) {
            $db = Post::db();
            $stmt = $db->query('SELECT MAX(menu_order) as max_order FROM posts WHERE is_in_menu = 1');
            $maxOrder = $stmt->fetch()['max_order'] ?? 0;
            $post->setMenuOrder($maxOrder + 1);
        }

        $post->save();

        Session::flash('success', 'Menu mis à jour avec succès');
        header('Location: /admin/posts');
    }

    #[Route('/admin/posts/{id}/move-up', 'POST')]
    public function moveUp(int $id): void
    {
        AdminMiddleware::handle();
        CsrfMiddleware::handle();

        $post = Post::find($id);

        if (!$post || !$post->getIsInMenu()) {
            Session::flash('error', 'Article introuvable');
            header('Location: /admin/posts');
            exit;
        }

        $currentOrder = $post->getMenuOrder();

        // Trouver le post juste au-dessus
        $db = Post::db();
        $stmt = $db->prepare('SELECT * FROM posts WHERE is_in_menu = 1 AND menu_order < ? ORDER BY menu_order DESC LIMIT 1');
        $stmt->execute([$currentOrder]);
        $result = $stmt->fetch();

        if ($result) {
            $upperPost = new Post($result);
            $upperOrder = $upperPost->getMenuOrder();

            // Échanger les ordres
            $post->setMenuOrder($upperOrder);
            $upperPost->setMenuOrder($currentOrder);

            $post->save();
            $upperPost->save();
        }

        header('Location: /admin/posts');
    }

    #[Route('/admin/posts/{id}/move-down', 'POST')]
    public function moveDown(int $id): void
    {
        AdminMiddleware::handle();
        CsrfMiddleware::handle();

        $post = Post::find($id);

        if (!$post || !$post->getIsInMenu()) {
            Session::flash('error', 'Article introuvable');
            header('Location: /admin/posts');
            exit;
        }

        $currentOrder = $post->getMenuOrder();

        // Trouver le post juste en-dessous
        $db = Post::db();
        $stmt = $db->prepare('SELECT * FROM posts WHERE is_in_menu = 1 AND menu_order > ? ORDER BY menu_order ASC LIMIT 1');
        $stmt->execute([$currentOrder]);
        $result = $stmt->fetch();

        if ($result) {
            $lowerPost = new Post($result);
            $lowerOrder = $lowerPost->getMenuOrder();

            // Échanger les ordres
            $post->setMenuOrder($lowerOrder);
            $lowerPost->setMenuOrder($currentOrder);

            $post->save();
            $lowerPost->save();
        }

        header('Location: /admin/posts');
    }

    /**
     * Réorganise automatiquement les ordres du menu pour éliminer les doublons
     */
    private function reorderMenuItems(): void
    {
        $db = Post::db();
        $stmt = $db->query('SELECT * FROM posts WHERE is_in_menu = 1 ORDER BY menu_order ASC, id ASC');
        $menuPosts = $stmt->fetchAll();

        $order = 1;
        foreach ($menuPosts as $postData) {
            $db->prepare('UPDATE posts SET menu_order = ? WHERE id = ?')
                ->execute([$order, $postData['id']]);
            $order++;
        }
    }
}
