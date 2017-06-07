<?php

namespace Yap\Foundation\Validators;

use Illuminate\Auth\AuthManager;

class NotCurrentUser
{

    /**
     * @var \Illuminate\Auth\AuthManager
     */
    protected $auth;


    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }


    public function validate($attribute, $value, $parameters, $validator)
    {
        return $this->auth->user()->id != $value;
    }

}