<?php

use Faker\Generator as Faker;

$factory->define(App\Project::class, function (Faker $faker) {
    return [
        'project_name' => substr($faker->sentence(2), 0, -1),
        'live_url' => $faker->url,
        'test_url' => $faker->url,
        'live_credentials_user' => $faker->password,
        'live_credentials_pass' => $faker->password,
        'test_credentials_user' => $faker->password,
        'test_credentials_pass' => $faker->password
    ];
});
