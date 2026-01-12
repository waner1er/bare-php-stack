<?php

use Faker\Factory;
use App\Domain\Entity\User;

return function () {
    $faker = Factory::create();
    $count = 20;

    $admin = new User([
        'first_name' => 'admin',
        'last_name' => 'admin',
        'email' => 'admin@admin.com',
        'password' => password_hash('password', PASSWORD_DEFAULT),
        'role' => 'admin',
    ]);
    $admin->save();

    for ($i = 0; $i < $count; $i++) {
        $user = new User([
            'first_name' => $faker->firstName(),
            'last_name' => $faker->lastName(),
            'email' => $faker->email(),
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'role' => 'user',
        ]);
        $user->save();
    }

    echo "  ✓ {$count} users créés\n";
};
