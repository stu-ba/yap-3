<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Yap\Models\Project::class, function (Faker\Generator $faker) {
    return [
        'github_team_id' => null,
        'github_repository_id' => null,
        'taiga_id' => null,
        'project_type_id' => rand(1,4),
        'name' => $faker->domainName,
        'description' => $faker->paragraph(1),
        'is_archived' => false,
        'archive_at' => null,
    ];
});

