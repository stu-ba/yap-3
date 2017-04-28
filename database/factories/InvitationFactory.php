<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Yap\Models\Invitation::class, function (Faker\Generator $faker) {
    return [
        'user_id' => factory(Yap\Models\User::class)->states(['confirmed'])->create()->id,
        'email' => $faker->unique()->safeEmail,
        'depleted_at' => \Carbon\Carbon::now()->addDay(rand(1, 5)),
    ];
});

$factory->defineAs(Yap\Models\Invitation::class, 'unconfirmed', function (Faker\Generator $faker) {
    return [
        'user_id' => factory(Yap\Models\User::class)->create()->id,
        'email' => $faker->unique()->safeEmail,
        'depleted_at' => \Carbon\Carbon::now()->addDay(rand(1, 5)),
    ];
});

$factory->defineAs(Yap\Models\Invitation::class, 'admin', function (Faker\Generator $faker) {
    return [
        'user_id' => factory(Yap\Models\User::class)->states(['admin', 'confirmed'])->create()->id,
        'email' => $faker->unique()->safeEmail,
        'depleted_at' => \Carbon\Carbon::now()->addDay(rand(1, 5)),
    ];
});

$factory->defineAs(Yap\Models\Invitation::class, 'banned', function (Faker\Generator $faker) {
    return [
        'user_id' => factory(Yap\Models\User::class)->states(['banned', 'confirmed'])->create()->id,
        'email' => $faker->unique()->safeEmail,
        'depleted_at' => \Carbon\Carbon::now()->addDay(rand(1, 5)),
    ];
});

$factory->defineAs(Yap\Models\Invitation::class, 'empty', function (Faker\Generator $faker) {
    return [
        'user_id' => factory(Yap\Models\User::class, 'empty')->create()->id,
        'email' => $faker->unique()->safeEmail,
        'depleted_at' => null,
    ];
});



$factory->state(Yap\Models\Invitation::class, 'depleted', function () {
    return [
        'depleted_at' => \Carbon\Carbon::now()->subDay(),
    ];
});

$factory->state(Yap\Models\Invitation::class, '!depleted', function () {
    return [
        'depleted_at' => null,
    ];
});

$factory->state(Yap\Models\Invitation::class, 'expired', function () {
    return [
        'valid_until' => \Carbon\Carbon::now()->subDay(),
    ];
});
