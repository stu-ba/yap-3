<?php

namespace Yap\Auxiliary;

class TaigaChecker extends HttpStatusChecker
{
    public function __construct()
    {
        $this->url = config('yap.taiga.api');
    }
}