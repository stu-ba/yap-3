<?php

namespace Tests;

trait ForceSyncQueueDriver
{
    public function forceSyncQueueDriver()
    {
        $this->app['queue']->setDefaultDriver('sync');
    }
}
