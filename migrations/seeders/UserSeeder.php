<?php

// Seeder: User
// Génère des données pour la table 'users'

use Faker\Factory;
use App\Infrastructure\Database\Database;

return function () {
    $faker = Factory::create();
    $pdo = Database::getConnection();

    $count = 20;

    // Créer l'utilisateur admin
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        'admin',
        'admin',
        'admin@admin.com',
        password_hash('password', PASSWORD_DEFAULT),
        'admin' // Rôle admin
    ]);

    // Créer les utilisateurs normaux
    for ($i = 0; $i < $count; $i++) {
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $faker->firstName(),
            $faker->lastName(),
            $faker->email(),
            password_hash('password', PASSWORD_DEFAULT),
            'user' // Rôle user
        ]);
    }

    echo "  ✓ {$count} users créés\n";
};
