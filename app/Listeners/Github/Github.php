<?php

namespace Yap\Listeners\Github;

use Illuminate\Contracts\Queue\ShouldQueue;
use Yap\Auxiliary\GithubApi;

abstract class Github implements ShouldQueue
{

    public $queue = 'github';

    /**@var GitHubManager $github */
    protected $github;

    protected $rootTeamId;


    /**
     * Github constructor.
     *
     * @param \Yap\Auxiliary\GithubApi $github
     */
    public function __construct(GithubApi $github)
    {
        $this->github     = $github;
        $this->rootTeamId = config('yap.github.root_team.id');
    }
}