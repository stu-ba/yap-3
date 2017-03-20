<?php

if ( ! function_exists('systemAccount')) {
    function systemAccount()
    {
        $user = resolve(Yap\Models\User::class);

        return $user->system() ?? factory(Yap\Models\User::class, 'system')->create();
    }
}

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Yap\Models\User::class, function (Faker\Generator $faker) {
    return [
        'taiga_id'       => $faker->randomNumber(9, true),
        'github_id'      => $faker->randomNumber(9, true),
        'email'          => $faker->unique()->safeEmail,
        'username'       => $faker->userName,
        'name'           => $faker->firstName.' '.$faker->lastName,
        'bio'            => \Illuminate\Foundation\Inspiring::quote(),
        'ban_reason'     => null,
        'is_admin'       => false,
        'is_banned'      => false,
        'is_confirmed'   => true,
        'remember_token' => str_random(64),
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->defineAs(Yap\Models\User::class, 'system', function () {
    $user = resolve(Yap\Models\User::class);

    if ($user->system()) {
        throw new Exception('System account already exits.');
    }

    return [
        'taiga_id'       => null,
        'github_id'      => null,
        'email'          => Config::get('mail.from.address'),
        'username'       => 'Neo',
        'name'           => 'Thomas A. Anderson',
        'bio'            => 'I followed the white rabbit.',
        'ban_reason'     => 'You invited yourself.',
        'is_admin'       => true,
        'is_banned'      => false,
        'is_confirmed'   => true,
        'remember_token' => null,
    ];
});

$factory->defineAs(Yap\Models\User::class, 'empty', function () {
    return [];
});

$factory->state(Yap\Models\User::class, 'admin', function () {
    return [
        'admin' => true,
    ];
});

$factory->state(Yap\Models\User::class, 'banned', function () {
    return [
        'is_banned'  => true,
        'ban_reason' => 'Because factory said so!'
    ];
});

$factory->state(Yap\Models\User::class, 'confirmed', function () {
    return [
        'is_confirmed' => true,
    ];
});