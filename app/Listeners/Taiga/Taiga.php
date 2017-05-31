<?php

namespace Yap\Listeners\Taiga;

use Illuminate\Contracts\Queue\ShouldQueue;
use Yap\Auxiliary\ApiAdaptors\Taiga as TaigaAdaptor;
use Yap\Auxiliary\HttpCheckers\Taiga as TaigaChecker;
use Yap\Listeners\DelayJob;

abstract class Taiga implements ShouldQueue
{

    use DelayJob;

    public $queue = 'taiga';

    public $timeout = 30;

    public $tries = 2;

    /**@var \TZK\Taiga\Taiga $taiga */
    protected $taiga;


    public function __construct(TaigaAdaptor $taiga, TaigaChecker $checker)
    {
        //TODO: catch exceptions
        $this->taiga   = $taiga;
        $this->checker = $checker;
        $this->delay   = config('yap.taiga.queue_delay');
    }
}