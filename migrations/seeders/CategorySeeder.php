<?php

// Seeder: Category
// Génère des données pour la table 'categories'

use App\Infrastructure\Database\Database;
use App\Infrastructure\Utils\StringHelper;

return function () {
    $pdo = Database::getConnection();

    $categories = [
        [
            'name' => 'Développement Web',
            'description' => 'Articles et projets liés au développement web'
        ],
        [
            'name' => 'Design',
            'description' => 'Créations graphiques et UI/UX'
        ],
        [
            'name' => 'Tutoriels',
            'description' => 'Guides et tutoriels techniques'
        ],
        [
            'name' => 'Projets',
            'description' => 'Mes réalisations et études de cas'
        ],
    ];

    foreach ($categories as $categoryData) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
        $stmt->execute([
            $categoryData['name'],
            StringHelper::slugify($categoryData['name']),
            $categoryData['description']
        ]);
    }

    echo "  ✓ " . count($categories) . " categories créées\n";
};
