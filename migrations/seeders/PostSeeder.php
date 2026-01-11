<?php

// Seeder: Post
// Génère des données pour la table 'posts'

use Faker\Factory;
use App\Infrastructure\Database\Database;
use App\Infrastructure\Utils\StringHelper;


return function () {
    $faker = Factory::create();
    $pdo = Database::getConnection();

    $stmt = $pdo->query("SELECT id FROM users");
    $userIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

    if (empty($userIds)) {
        echo "  ⚠ Aucun utilisateur trouvé. Exécutez d'abord UserSeeder.\n";
        return;
    }

    // Créer le post de la page d'accueil
    $stmt = $pdo->prepare("INSERT INTO posts (title, slug, content, user_id, is_in_menu, menu_order) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'Accueil',
        'home',
        '<h1>Bienvenue sur notre site</h1><p>Ceci est la page d\'accueil.</p>',
        $userIds[0], // Premier utilisateur (admin)
        0, // Pas dans le menu (car c'est la home)
        0
    ]);

    // Créer quelques pages pour le menu
    $menuPages = [
        ['À propos', 'a-propos', '<h1>À propos de nous</h1><p>Découvrez notre histoire.</p>', 1],
        ['Services', 'services', '<h1>Nos services</h1><p>Voici ce que nous proposons.</p>', 2],
        ['Contact', 'contact', '<h1>Contactez-nous</h1><p>N\'hésitez pas à nous écrire.</p>', 3],
    ];

    foreach ($menuPages as $page) {
        $stmt = $pdo->prepare("INSERT INTO posts (title, slug, content, user_id, is_in_menu, menu_order) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $page[0],
            $page[1],
            $page[2],
            $userIds[0],
            1, // Dans le menu
            $page[3]
        ]);
    }

    $count = 20;

    for ($i = 0; $i < $count; $i++) {
        $stmt = $pdo->prepare("INSERT INTO posts (title, slug, content, user_id, is_in_menu, menu_order) VALUES (?, ?, ?, ?, ?, ?)");
        $title = $faker->sentence(3);
        $stmt->execute([
            $title,
            StringHelper::slugify($title),
            $faker->paragraphs(3, true),
            $faker->randomElement($userIds),
            0, // Pas dans le menu par défaut
            0
        ]);
    }

    echo "  ✓ 1 post 'home' + 3 pages de menu + {$count} posts créés\n";
};
