<?php

// Seeder: Category
// Génère des données pour la table 'categories'

use App\Domain\Entity\Category;
use App\Infrastructure\Utils\StringHelper;

return function () {
    $categories = [
        [
            'name' => 'Développement Web',
            'description' => 'Articles et projets liés au développement web',
        ],
        [
            'name' => 'Design',
            'description' => 'Créations graphiques et UI/UX',
        ],
        [
            'name' => 'Tutoriels',
            'description' => 'Guides et tutoriels techniques',
        ],
        [
            'name' => 'Projets',
            'description' => 'Mes réalisations et études de cas',
        ],
    ];

    foreach ($categories as $categoryData) {
        $category = new Category([
            'name' => $categoryData['name'],
            'slug' => StringHelper::slugify($categoryData['name']),
            'description' => $categoryData['description'],
        ]);
        $category->save();
    }

    echo "  ✓ " . count($categories) . " categories créées\n";
};
