<?php

declare(strict_types=1);

namespace App\Interface\Admin\Controller;

use App\Domain\Repository\MenuItemRepositoryInterface;
use App\Domain\Repository\CategoryRepositoryInterface;
use App\Infrastructure\Repository\MenuItemRepository;
use App\Infrastructure\Repository\CategoryRepository;
use App\Infrastructure\Utils\SlugValidator;
use App\Interface\Common\Attribute\Route;
use App\Interface\Common\BaseController;
use App\Infrastructure\Middleware\AdminMiddleware;
use App\Infrastructure\Middleware\CsrfMiddleware;
use App\Infrastructure\Session\Session;
use App\Interface\FrontEnd\Component\MenuManager;

class MenuAdminController extends BaseController
{
    public function __construct(
        private MenuItemRepositoryInterface $menuItemRepository = new MenuItemRepository(),
        private CategoryRepositoryInterface $categoryRepository = new CategoryRepository(),
    ) {}

    #[Route('/admin/menu', 'GET')]
    public function index(): void
    {
        AdminMiddleware::handle();

        $menuItems = $this->menuItemRepository->findAll();
        usort($menuItems, fn($a, $b) => $a->getPosition() <=> $b->getPosition());

        // Récupérer les slugs déjà utilisés dans le menu
        $currentMenuSlugs = array_map(fn($item) => $item->getSlug(), $menuItems);

        // Récupérer uniquement les slugs disponibles non utilisés
        $availableSlugs = MenuManager::getAvailableSlugs($currentMenuSlugs);

        $categories = $this->categoryRepository->findAll();

        // Préparer les données des catégories pour les cards
        $categoryCards = [];
        foreach ($categories as $category) {
            $categoryCards[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'slug' => $category->getSlug(),
            ];
        }

        $this->render('menu.index', [
            'menuItems' => $menuItems,
            'availableSlugs' => $availableSlugs,
            'categories' => $categories,
            'categoryCards' => $categoryCards,
            'success' => Session::get('success'),
            'error' => Session::get('error'),
        ]);
    }

    #[Route('/admin/menu/add', 'POST')]
    public function add(): void
    {
        AdminMiddleware::handle();
        CsrfMiddleware::handle();

        $label = $_POST['label'] ?? '';
        $slug = $_POST['slug'] ?? '';
        $type = $_POST['type'] ?? 'custom';
        $categoryId = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;

        if (empty($label) || empty($slug)) {
            Session::flash('error', 'Le label et le slug sont obligatoires');
            header('Location: /admin/menu');
            exit;
        }

        // Vérifier que le slug n'est pas déjà dans le menu
        $existingItems = $this->menuItemRepository->findAll();
        foreach ($existingItems as $item) {
            if ($item->getSlug() === $slug) {
                Session::flash('error', "Le slug '{$slug}' est déjà dans le menu");
                header('Location: /admin/menu');
                exit;
            }
        }

        // Pour les slugs personnalisés (non-post), vérifier qu'ils sont disponibles
        if ($type !== 'post' && !SlugValidator::isSlugAvailable($slug)) {
            Session::flash('error', "Le slug '{$slug}' est déjà utilisé ou réservé");
            header('Location: /admin/menu');
            exit;
        }

        // Calculer la position maximale + 1
        $items = $this->menuItemRepository->findAll();
        $maxPosition = 0;
        foreach ($items as $item) {
            $maxPosition = max($maxPosition, $item->getPosition());
        }

        $menuItem = new \App\Domain\Entity\MenuItem([
            'label' => $label,
            'slug' => $slug,
            'type' => $type,
            'position' => $maxPosition + 1,
            'is_visible' => true,
            'category_id' => $categoryId,
        ]);

        $this->menuItemRepository->save($menuItem);

        Session::flash('success', 'Élément de menu ajouté avec succès');
        header('Location: /admin/menu');
    }

    #[Route('/admin/menu/{id}/toggle-visibility', 'POST')]
    public function toggleVisibility(int $id): void
    {
        AdminMiddleware::handle();
        CsrfMiddleware::handle();

        $menuItem = $this->menuItemRepository->find($id);

        if (!$menuItem) {
            Session::flash('error', 'Élément de menu introuvable');
            header('Location: /admin/menu');
            exit;
        }

        $menuItem->setIsVisible(!$menuItem->getIsVisible());
        $this->menuItemRepository->save($menuItem);

        Session::flash('success', 'Visibilité mise à jour');
        header('Location: /admin/menu');
    }

    #[Route('/admin/menu/{id}/move-up', 'POST')]
    public function moveUp(int $id): void
    {
        AdminMiddleware::handle();
        CsrfMiddleware::handle();

        $menuItem = $this->menuItemRepository->find($id);

        if (!$menuItem) {
            Session::flash('error', 'Élément de menu introuvable');
            header('Location: /admin/menu');
            exit;
        }

        $currentPosition = $menuItem->getPosition();

        $allItems = $this->menuItemRepository->findAll();
        usort($allItems, fn($a, $b) => $b->getPosition() <=> $a->getPosition());

        $upperItem = null;
        foreach ($allItems as $item) {
            if ($item->getPosition() < $currentPosition) {
                $upperItem = $item;
                break;
            }
        }

        if ($upperItem) {
            $upperPosition = $upperItem->getPosition();
            $menuItem->setPosition($upperPosition);
            $upperItem->setPosition($currentPosition);
            $this->menuItemRepository->save($menuItem);
            $this->menuItemRepository->save($upperItem);
        }

        header('Location: /admin/menu');
    }

    #[Route('/admin/menu/{id}/move-down', 'POST')]
    public function moveDown(int $id): void
    {
        AdminMiddleware::handle();
        CsrfMiddleware::handle();

        $menuItem = $this->menuItemRepository->find($id);

        if (!$menuItem) {
            Session::flash('error', 'Élément de menu introuvable');
            header('Location: /admin/menu');
            exit;
        }

        $currentPosition = $menuItem->getPosition();

        $allItems = $this->menuItemRepository->findAll();
        usort($allItems, fn($a, $b) => $a->getPosition() <=> $b->getPosition());

        $lowerItem = null;
        foreach ($allItems as $item) {
            if ($item->getPosition() > $currentPosition) {
                $lowerItem = $item;
                break;
            }
        }

        if ($lowerItem) {
            $lowerPosition = $lowerItem->getPosition();
            $menuItem->setPosition($lowerPosition);
            $lowerItem->setPosition($currentPosition);
            $this->menuItemRepository->save($menuItem);
            $this->menuItemRepository->save($lowerItem);
        }

        header('Location: /admin/menu');
    }

    #[Route('/admin/menu/{id}/delete', 'POST')]
    public function delete(int $id): void
    {
        AdminMiddleware::handle();
        CsrfMiddleware::handle();

        $menuItem = $this->menuItemRepository->find($id);

        if (!$menuItem) {
            Session::flash('error', 'Élément de menu introuvable');
            header('Location: /admin/menu');
            exit;
        }

        $this->menuItemRepository->delete($menuItem);

        Session::flash('success', 'Élément de menu supprimé');
        header('Location: /admin/menu');
    }
}
