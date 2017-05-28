<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Yap\Models\ProjectType::class, function (Faker\Generator $faker) {
    return [
        'taiga_id' => rand(1,10),
        'name' => $faker->userName,
        'description' => $faker->sentence
    ];
});

