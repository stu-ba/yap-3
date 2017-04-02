<?php

namespace Tests;

trait ForceArrayMailerDriver
{
    public function forceArrayMailerDriver()
    {
        $this->app['swift.transport']->setDefaultDriver('array');
    }
}