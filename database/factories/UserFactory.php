<?php

if ( ! function_exists('systemAccount')) {
    function systemAccount()
    {
        $user = resolve(Yap\Models\User::class);
        $system = $user->system();

        if ($system === null) {
            $system = factory(Yap\Models\User::class, 'system')->create();
        }

        return $system;
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
        'is_admin'       => false,
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
        'taiga_id'       => 0,
        'github_id'      => 0,
        'email'          => Config::get('mail.from.address'),
        'username'       => 'Neo',
        'name'           => 'Thomas A. Anderson',
        'bio'            => 'I followed the white rabbit.',
        'is_admin'       => true,
        'remember_token' => null,
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->defineAs(Yap\Models\User::class, 'empty', function () {
    return [];
});