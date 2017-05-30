<?php

return [
    'short_name'   => 'Yap',
    'invitations'  => [
        //in days
        'valid_until' => 7,
    ],
    'github'       => [
        'organisation' => 'stu-ba',
        'source_code'  => 'https://github.com/stu-ba/yap-3/tree/stable',
        // id of ytrium user (main user of yap application) for github
        'id'           => 26739569,
        'root_team'    => [
            'id'   => 2298259,
            'name' => 'yap-root',
        ],
    ],
    'taiga'        => [
        'api'  => env('TAIGA_API', null),
        'site' => env('TAIGA_SITE', null),
        // id of ytrium user (main user of yap application) for taiga
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
        'admin'        => 'fa-cog',
        'remove'       => 'fa-times-rectangle',
        'add'          => 'fa-plus-square',
        'detail'       => 'fa-eye',
        'unread'       => 'fa-eye-slash',
        'read'         => 'fa-eye',
        'promote'      => 'fa-hand-o-up',
        'demote'       => 'fa-hand-o-down',
        'edit'         => 'fa-pencil',
        'colleagues'   => 'fa-users',
        'ban'          => 'fa-ban',
        'banned'       => 'fa-user-times',
        'all'          => 'fa-asterisk',
        'invite'       => 'fa-user-plus',
        'project'      => 'fa-briefcase',
        'system'       => 'fa-cogs',
    ],
    'placeholders' => [
        'name' => 'Joe Little Carrot',
        'bio'  => 'I simply can not code in ones and zeros.',
    ],
];
