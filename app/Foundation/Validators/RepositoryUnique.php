<?php

namespace Yap\Foundation\Validators;

use Yap\Auxiliary\ApiAdaptors\Github as GithubAdaptor;

class RepositoryUnique
{

    /**
     * @var \Yap\Auxiliary\ApiAdaptors\Github
     */
    protected $github;


    public function __construct(GithubAdaptor $github)
    {
        $this->github = $github;
    }


    public function validate($attribute, $value, $parameters, $validator)
    {
        return ! $this->github->checkRepositoryExists(str_slug($value));
    }

}