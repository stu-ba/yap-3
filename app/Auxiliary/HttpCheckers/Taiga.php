<?php

namespace Yap\Auxiliary\HttpCheckers;

class Taiga extends Checker
{
    public function __construct()
    {
        $this->url = config('yap.taiga.api');
    }
}