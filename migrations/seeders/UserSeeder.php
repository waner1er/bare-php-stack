<?php

// Seeder: User
// Génère des données pour la table 'users'

use Faker\Factory;
use App\Infrastructure\Database\Database;

return function () {
    $faker = Factory::create();
    $pdo = Database::getConnection();

    $count = 20;

    // default admin user
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        'admin',
        'admin',
        'admin@admin.com',
        password_hash('password', PASSWORD_DEFAULT) // Mot de passe hashé
    ]);

    for ($i = 0; $i < $count; $i++) {
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $faker->firstName(),
            $faker->lastName(),
            $faker->email(),
            password_hash('password', PASSWORD_DEFAULT) // Mot de passe hashé
        ]);
    }

    echo "  ✓ {$count} users créés\n";
};
