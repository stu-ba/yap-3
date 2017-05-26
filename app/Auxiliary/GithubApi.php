<?php

namespace Yap\Auxiliary;

use GrahamCampbell\GitHub\GitHubManager;

class GithubApi
{

    protected $github;

    protected $organization;


    public function __construct(GitHubManager $github)
    {
        $this->github       = $github;
        $this->organization = config('yap.github.organisation');
    }


    public function rateLimits()
    {
        return $this->github->rateLimit()->getRateLimits();
    }


    public function addToTeam(int $teamId, string $username)
    {
        return $this->github->teams()->addMember($teamId, $username);
    }


    public function removeFromTeam(int $teamId, string $username)
    {
        return $this->github->teams()->removeMember($teamId, $username);
    }


    public function createTeam(string $name, string $description = '')
    {
        //TODO: catch for name already exists...
        return $this->github->teams()->create($this->organization,
            ['name' => $name, 'description' => $description, 'privacy' => 'closed']);
    }


    public function createRepository(string $name, string $description, int $teamId)
    {
        //TODO: catch for name already exists...
        return $this->github->repository()
                            ->create($name, $description, '', true, $this->organization, true, false,
                                false, $teamId, false);
    }


    public function getRepositoryById(int $id)
    {
        //TODO: catch for id does not exist
        return $this->github->repository()->showById($id);
    }

}