<?php

use App\Domain\Entity\Post;
use App\Domain\Entity\User;
use Faker\Factory;
use App\Infrastructure\Utils\StringHelper;

return function () {
    $faker = Factory::create();
    $userIds = array_map(fn($user) => $user->getId(), User::all());
    if (empty($userIds)) {
        echo "  ⚠ Aucun utilisateur trouvé. Exécutez d'abord UserSeeder.\n";
        return;
    }

    // // Créer le post de la page d'accueil
    // $homePost = new Post([
    //     'title' => 'Accueil',
    //     'slug' => 'home',
    //     'content' => '<h1>Bienvenue sur notre site</h1><p>Ceci est la page d\'accueil.</p>',
    //     'user_id' => $userIds[0],
    //     'is_in_menu' => 0,
    //     'menu_order' => 0,
    // ]);
    // $homePost->save();

    $menuPages = [
        ['À propos', 'a-propos', '<h1>À propos de nous</h1><p>Découvrez notre histoire.</p>', 1],
        ['Services', 'services', '<h1>Nos services</h1><p>Voici ce que nous proposons.</p>', 2],
        ['Contact', 'contact', '<h1>Contactez-nous</h1><p>N\'hésitez pas à nous écrire.</p>', 3],
    ];

    foreach ($menuPages as $page) {
        $post = new Post([
            'title' => $page[0],
            'slug' => $page[1],
            'content' => $page[2],
            'user_id' => $userIds[0],
            'is_in_menu' => 1,
            'menu_order' => $page[3],
        ]);
        $post->save();
    }

    $count = 20;

    for ($i = 0; $i < $count; $i++) {
        $title = $faker->sentence(3);
        $post = new Post([
            'title' => $title,
            'slug' => StringHelper::slugify($title),
            'content' => $faker->paragraphs(3, true),
            'user_id' => $faker->randomElement($userIds),
            'is_in_menu' => 0,
            'category_id' => rand(1, 4),
            'menu_order' => 0,
        ]);
        $post->save();
    }

    echo "  ✓ 1 post 'home' + 3 pages de menu + {$count} posts créés\n";
};
