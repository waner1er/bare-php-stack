<?php

use App\Domain\Entity\Category;
use App\Infrastructure\Repository\CategoryRepository;
use App\Infrastructure\Utils\StringHelper;

return function () {
    $repository = new CategoryRepository();

    $categories = [
        ['name' => 'Développement Web', 'description' => 'Articles et projets liés au développement web'],
        ['name' => 'Design', 'description' => 'Créations graphiques et UI/UX'],
        ['name' => 'Tutoriels', 'description' => 'Guides et tutoriels techniques'],
        ['name' => 'Projets', 'description' => 'Mes réalisations et études de cas'],
    ];

    foreach ($categories as $data) {
        $category = new Category([
            'name' => $data['name'],
            'slug' => StringHelper::slugify($data['name']),
            'description' => $data['description'],
        ]);
        $repository->save($category);
    }

    echo "  ✓ " . count($categories) . " categories créées\n";
};
