<?php

// Seeder: Post
// Génère des données pour la table 'posts'

use PDO;
use Faker\Factory;
use App\Infrastructure\Database\Database;
use App\Infrastructure\Utils\StringHelper;


return function () {
    $faker = Factory::create();
    $pdo = Database::getConnection();

    // Récupérer les IDs des utilisateurs existants
    $stmt = $pdo->query("SELECT id FROM users");
    $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($userIds)) {
        echo "  ⚠ Aucun utilisateur trouvé. Exécutez d'abord UserSeeder.\n";
        return;
    }

    $count = 20;

    for ($i = 0; $i < $count; $i++) {
        $stmt = $pdo->prepare("INSERT INTO posts (title, slug, content, user_id) VALUES (?, ?, ?, ?)");
        $title = $faker->sentence(3);
        $stmt->execute([
            $title,
            StringHelper::slugify($title),
            $faker->paragraphs(3, true),
            $faker->randomElement($userIds) // Utilise un ID existant
        ]);
    }

    echo "  ✓ {$count} posts créés\n";
};
