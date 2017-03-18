<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Yap\Models\Invitation::class, function (Faker\Generator $faker) {
    $system = systemAccount();

    return [
        'user_id'     => factory(Yap\Models\User::class)->create()->id,
        'created_by'  => $system->id,
        'email'       => $faker->unique()->safeEmail,
        'token'       => base64_encode(str_random(64)),
        'is_depleted' => true,
        'depleted_at' => \Carbon\Carbon::now()->addDay(rand(1, 5)),
        'valid_until' => \Carbon\Carbon::now()->addWeek(),
    ];
});

$factory->defineAs(Yap\Models\Invitation::class, 'empty', function (Faker\Generator $faker) {
    $system = systemAccount();

    return [
        'user_id'     => factory(Yap\Models\User::class, 'empty')->create()->id,
        'created_by'  => $system->id,
        'email'       => $faker->unique()->safeEmail,
        'token'       => base64_encode(str_random(64)),
        'is_depleted' => false,
        'depleted_at' => null,
        'valid_until' => \Carbon\Carbon::now()->addWeek(),
    ];
});

$factory->state(Yap\Models\Invitation::class, 'depleted', function () {
    return [
        'is_depleted' => true,
        'depleted_at' => \Carbon\Carbon::now()->subDay()
    ];
});