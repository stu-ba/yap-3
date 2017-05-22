<?php

namespace Yap\Auxiliary;

use GrahamCampbell\GitHub\GitHubManager;

class GithubApi
{

    protected $github;


    public function __construct(GitHubManager $github)
    {
        $this->github = $github;
    }

    public function rateLimits()
    {
        return $this->github->rateLimit()->getRateLimits();
    }

    public function addToTeam($teamId, $username)
    {
        return $this->github->teams()->addMember($teamId, $username);
    }

    public function removeFromTeam($teamId, $username)
    {
        return $this->github->teams()->removeMember($teamId, $username);
    }

}