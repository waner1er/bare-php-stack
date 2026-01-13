<?php

declare(strict_types=1);

namespace App\Interface\Admin\Crud;

use App\Application\Service\Crud\CrudResource;
use App\Application\Service\Crud\Input\TextInput;
use App\Application\Service\Crud\Input\NumberInput;
use App\Application\Service\Crud\Input\SelectInput;
use App\Application\Service\Crud\Input\TextareaInput;
use App\Application\Service\Crud\Column\TextColumn;
use App\Application\Service\Crud\Column\NumberColumn;
use App\Application\Service\Crud\Column\BooleanColumn;
use App\Application\Service\Crud\Column\DateColumn;
use App\Application\Service\Crud\Column\RelationColumn;
use App\Domain\Entity\Post;
use App\Domain\Entity\User;

class PostResource extends CrudResource
{
    protected string $entityClass = Post::class;
    protected string $title = 'Posts';
    protected string $singularTitle = 'Post';

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
        $users = User::all();
        $userOptions = [];
        foreach ($users as $user) {
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
        $categories = \App\Domain\Entity\Category::all();
        $options = ['' => 'Aucune'];

        foreach ($categories as $category) {
            $options[$category->getId()] = $category->getName();
        }

        return $options;
    }
}
