<?php

namespace Tests;

trait ForceSyncQueueDriver
{

    public function forceSyncDriver()
    {
        $this->app['queue']->setDefaultDriver('sync');
    }
}