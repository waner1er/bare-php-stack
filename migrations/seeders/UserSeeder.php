<?php

use App\Domain\Entity\User;
use App\Infrastructure\Repository\UserRepository;
use Faker\Factory;

return function () {
    $faker = Factory::create();
    $repository = new UserRepository();
    $count = 20;

    $admin = new User([
        'first_name' => 'admin',
        'last_name' => 'admin',
        'email' => 'admin@admin.com',
        'password' => password_hash('password', PASSWORD_DEFAULT),
        'role' => 'admin',
    ]);
    $repository->save($admin);

    for ($i = 0; $i < $count; $i++) {
        $user = new User([
            'first_name' => $faker->firstName(),
            'last_name' => $faker->lastName(),
            'email' => $faker->email(),
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'role' => 'user',
        ]);
        $repository->save($user);
    }

    echo "  ✓ admin + {$count} users créés\n";
};
