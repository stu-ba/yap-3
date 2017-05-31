<?php

namespace Yap\Listeners\Github;

use Illuminate\Contracts\Queue\ShouldQueue;
use Yap\Auxiliary\ApiAdaptors\Github as GithubAdaptor;
use Yap\Auxiliary\HttpCheckers\Github as GithubChecker;
use Yap\Listeners\DelayJob;

abstract class Github implements ShouldQueue
{

    use DelayJob;

    public $queue = 'github';

    public $timeout = 30;

    public $tries = 2;

    /**@var GitHubManager $github */
    protected $github;

    protected $rootTeamId;


    /**
     * Github constructor.
     *
     * @param \Yap\Auxiliary\ApiAdaptors\Github  $github
     * @param \Yap\Auxiliary\HttpCheckers\Github $checker
     */
    public function __construct(GithubAdaptor $github, GithubChecker $checker)
    {
        $this->github     = $github;
        $this->checker    = $checker;
        $this->rootTeamId = config('yap.github.root_team.id');
        $this->delay      = config('yap.taiga.queue_delay');
    }
}