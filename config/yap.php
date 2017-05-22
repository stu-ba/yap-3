<?php

return [
    'short_name'   => 'Yap',
    'invitations'  => [
        //in days
        'valid_until' => 7,
    ],
    'github'       => [
        'organisation' => 'stu-ba',
        'source_code'  => 'https://github.com/stu-ba/yap-3',
        // Id of ytrium user (main user of yap application) for github
        'id'           => 26739569,
        'root_team'    => [
            'id'   => '2298259',
            'name' => 'yap-root',
        ],
    ],
    'taiga'        => [
        'api'  => env('TAIGA_API', null),
        'site' => env('TAIGA_SITE', null),
        'id'   => 5,
    ],
    'icons'        => [
        'home'         => 'fa-home',
        'users'        => 'fa-users',
        'user'         => 'fa-user',
        'profile'      => 'fa-user-o',
        'notification' => 'fa-bell',
        'help'         => 'fa-support',
        'logout'       => 'fa-sign-out',
        'github'       => 'fa-github',
        'admin'        => 'fa-tty',
        'edit'         => 'fa-pencil',
        'colleagues'   => 'fa-users',
        'banned'       => 'fa-user-times',
        'all'          => 'fa-asterisk',
        'invite'       => 'fa-envelope',
        'project'      => 'fa-briefcase',
        'system'       => 'fa-cogs',
    ],
    'placeholders' => [
        'name' => 'Joe Little Carrot',
        'bio'  => 'I simply can not code in ones and zeros.',
    ],
];
