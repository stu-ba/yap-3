<?php

return [

    //Cache for ... minutes
    'cache_length' => 10,

    //Repository to grab documentation from
    'repository'   => 'https://github.com/stu-ba/yap-3-user-guide.git',

    //Main documentation file
    'main'         => 'releases',

    //Path to documentation files
    'path'         => 'resources/docs/',

    //Supported blockquote icons "name" => "svg file"
    'icons'        => [
        'note'     => 'exclamation-circle',
        'tip'      => 'lightbulb-o',
        'warning'  => 'exclamation',
        'video'    => 'film',
        'youtube'  => 'youtube-play',
        'github'   => 'github',
        'fork'     => 'code-fork',
        'overflow' => 'stack-overflow',
        'link'     => 'chain',
    ],
];
