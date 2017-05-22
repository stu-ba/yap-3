<?php

namespace Yap\Listeners\Taiga;

use Illuminate\Contracts\Queue\ShouldQueue;
use Yap\Auxiliary\TaigaApi;

abstract class Taiga implements ShouldQueue
{

    public $queue = 'taiga';

    /**@var \TZK\Taiga\Taiga $taiga */
    protected $taiga;


    public function __construct(TaigaApi $taiga)
    {
        //TODO: catch exceptions
        //TODO: go only if taiga is online!
        $this->taiga = $taiga;
    }
}