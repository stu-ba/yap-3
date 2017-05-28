<?php

namespace Yap\Listeners\Github;

use Illuminate\Contracts\Queue\ShouldQueue;
use Yap\Auxiliary\ApiAdaptors\Github as GithubAdaptor;

abstract class Github implements ShouldQueue
{

    public $queue = 'github';

    /**@var GitHubManager $github */
    protected $github;

    protected $rootTeamId;


    /**
     * Github constructor.
     *
     * @param \Yap\Auxiliary\ApiAdaptors\Github $github
     */
    public function __construct(GithubAdaptor $github)
    {
        $this->github     = $github;
        $this->rootTeamId = config('yap.github.root_team.id');
    }
}