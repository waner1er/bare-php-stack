<?php

declare(strict_types=1);

namespace App\Interface\Admin\Crud;

use App\Application\Service\Crud\Column\NumberColumn;
use App\Application\Service\Crud\Column\RelationColumn;
use App\Application\Service\Crud\Column\TextColumn;
use App\Application\Service\Crud\CrudResource;
use App\Application\Service\Crud\Input\SelectInput;
use App\Application\Service\Crud\Input\TextareaInput;
use App\Application\Service\Crud\Input\TextInput;
use App\Domain\Entity\Post;
use App\Domain\Repository\CategoryRepositoryInterface;
use App\Domain\Repository\PostRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Infrastructure\Repository\CategoryRepository;
use App\Infrastructure\Repository\PostRepository;
use App\Infrastructure\Repository\UserRepository;

class PostResource extends CrudResource
{
    protected string $entityClass = Post::class;
    protected string $title = 'Posts';
    protected string $singularTitle = 'Post';

    public function __construct(
        private PostRepositoryInterface $postRepository = new PostRepository(),
        private UserRepositoryInterface $userRepository = new UserRepository(),
        private CategoryRepositoryInterface $categoryRepository = new CategoryRepository(),
    ) {}

    public function repository(): PostRepositoryInterface
    {
        return $this->postRepository;
    }

    public function columns(): array
    {
        return [
            new NumberColumn('id', 'Id'),
            (new TextColumn('title', 'Title'))->setLimit(50),
            (new TextColumn('slug', 'Slug'))->setLimit(50),
            (new TextColumn('content', 'Content'))->setLimit(50),
            new RelationColumn('user_id', 'Auteur', 'user', 'firstName'),
            new RelationColumn('category_id', 'Category', 'getCategory', 'name'),
        ];
    }

    public function inputs(): array
    {
        $userOptions = [];
        foreach ($this->userRepository->findAll() as $user) {
            $userOptions[$user->getId()] = $user->getFirstName() . ' ' . $user->getLastName();
        }

        return [
            (new TextInput('title', 'Title'))->setRequired(true),
            (new TextInput('slug', 'Slug'))->setRequired(true),
            (new TextareaInput('content', 'Contenu'))->setRows(10)
                ->enableWysiwyg('tinymce'),
            (new SelectInput('user_id', 'Auteur'))->setOptions($userOptions)->setRequired(true),
            (new SelectInput('category_id', 'Category'))->setOptions($this->getCategoryOptions()),
        ];
    }

    private function getCategoryOptions(): array
    {
        $options = ['' => 'Aucune'];
        foreach ($this->categoryRepository->findAll() as $category) {
            $options[$category->getId()] = $category->getName();
        }
        return $options;
    }
}
