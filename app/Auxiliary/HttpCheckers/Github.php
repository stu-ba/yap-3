<?php

namespace Yap\Auxiliary\HttpCheckers;

class Github extends Checker
{

    public function __construct()
    {
        $this->url     = config('yap.github.api_limit');
        $this->timeout = 4;
    }
}