<?php

declare(strict_types=1);

namespace App\Interface\Admin\Controller;

use App\Domain\Repository\PostRepositoryInterface;
use App\Infrastructure\Repository\PostRepository;
use App\Interface\Common\Attribute\Route;
use App\Interface\Common\BaseController;
use App\Infrastructure\Middleware\AdminMiddleware;
use App\Infrastructure\Middleware\CsrfMiddleware;
use App\Infrastructure\Session\Session;

class PostAdminController extends BaseController
{
    public function __construct(private PostRepositoryInterface $postRepository = new PostRepository()) {}

    #[Route('/admin/posts', 'GET')]
    public function index(): void
    {
        AdminMiddleware::handle();

        $this->reorderMenuItems();

        $posts = $this->postRepository->findAll();
        usort(
            $posts,
            fn($a, $b)
            => [$b->getIsInMenu(), $a->getMenuOrder(), $a->getTitle()]
                <=> [$a->getIsInMenu(), $b->getMenuOrder(), $b->getTitle()],
        );

        $menuPositions = [];
        $position = 1;
        foreach ($posts as $post) {
            if ($post->getIsInMenu()) {
                $menuPositions[$post->getId()] = $position++;
            }
        }

        $this->render('posts.index', [
            'posts' => $posts,
            'menuPositions' => $menuPositions,
        ]);
    }

    #[Route('/admin/posts/{id}/toggle-menu', 'POST')]
    public function toggleMenu(int $id): void
    {
        AdminMiddleware::handle();
        CsrfMiddleware::handle();

        $post = $this->postRepository->find($id);

        if (!$post) {
            Session::flash('error', 'Article introuvable');
            header('Location: /admin/posts');
            exit;
        }

        $isInMenu = $post->getIsInMenu();
        $post->setIsInMenu(!$isInMenu);

        if (!$isInMenu) {
            $menuPosts = array_filter($this->postRepository->findAll(), fn($p) => $p->getIsInMenu());
            $maxOrder = 0;
            foreach ($menuPosts as $p) {
                $maxOrder = max($maxOrder, $p->getMenuOrder());
            }
            $post->setMenuOrder($maxOrder + 1);
        }

        $this->postRepository->save($post);

        Session::flash('success', 'Menu mis à jour avec succès');
        header('Location: /admin/posts');
    }

    #[Route('/admin/posts/{id}/move-up', 'POST')]
    public function moveUp(int $id): void
    {
        AdminMiddleware::handle();
        CsrfMiddleware::handle();

        $post = $this->postRepository->find($id);

        if (!$post || !$post->getIsInMenu()) {
            Session::flash('error', 'Article introuvable');
            header('Location: /admin/posts');
            exit;
        }

        $currentOrder = $post->getMenuOrder();

        $menuPosts = array_filter($this->postRepository->findAll(), fn($p) => $p->getIsInMenu());
        usort($menuPosts, fn($a, $b) => $b->getMenuOrder() <=> $a->getMenuOrder());

        $upperPost = null;
        foreach ($menuPosts as $p) {
            if ($p->getMenuOrder() < $currentOrder) {
                $upperPost = $p;
                break;
            }
        }

        if ($upperPost) {
            $upperOrder = $upperPost->getMenuOrder();

            $post->setMenuOrder($upperOrder);
            $upperPost->setMenuOrder($currentOrder);

            $this->postRepository->save($post);
            $this->postRepository->save($upperPost);
        }

        header('Location: /admin/posts');
    }

    #[Route('/admin/posts/{id}/move-down', 'POST')]
    public function moveDown(int $id): void
    {
        AdminMiddleware::handle();
        CsrfMiddleware::handle();

        $post = $this->postRepository->find($id);

        if (!$post || !$post->getIsInMenu()) {
            Session::flash('error', 'Article introuvable');
            header('Location: /admin/posts');
            exit;
        }

        $currentOrder = $post->getMenuOrder();

        $menuPosts = array_filter($this->postRepository->findAll(), fn($p) => $p->getIsInMenu());
        usort($menuPosts, fn($a, $b) => $a->getMenuOrder() <=> $b->getMenuOrder());

        $lowerPost = null;
        foreach ($menuPosts as $p) {
            if ($p->getMenuOrder() > $currentOrder) {
                $lowerPost = $p;
                break;
            }
        }

        if ($lowerPost) {
            $lowerOrder = $lowerPost->getMenuOrder();

            $post->setMenuOrder($lowerOrder);
            $lowerPost->setMenuOrder($currentOrder);

            $this->postRepository->save($post);
            $this->postRepository->save($lowerPost);
        }

        header('Location: /admin/posts');
    }

    private function reorderMenuItems(): void
    {
        $menuPosts = array_filter($this->postRepository->findAll(), fn($post) => $post->getIsInMenu());
        usort($menuPosts, fn($a, $b) => $a->getMenuOrder() <=> $b->getMenuOrder());

        $order = 1;
        foreach ($menuPosts as $post) {
            $post->setMenuOrder($order++);
            $this->postRepository->save($post);
        }
    }
}
