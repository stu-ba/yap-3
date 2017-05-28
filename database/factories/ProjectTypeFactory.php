<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Yap\Models\ProjectType::class, function (Faker\Generator $faker) {
    return [
        'taiga_id' => $faker->randomNumber(),
        'name' => $faker->userName,
        'description' => $faker->sentence
    ];
});

