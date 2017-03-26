<?php

namespace Tests;

use Exception;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;
use Yap\Exceptions\Handler;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public $baseUrl = 'http://test.dev';


    protected function setUpTraits()
    {
        parent::setUpTraits();

        $uses = array_flip(class_uses_recursive(static::class));

        if (isset($uses[ForceSyncQueueDriver::class])) {
            $this->forceSyncDriver();
        }
    }


    /**
     * Disable Laravel's exception handling.
     *
     * @return $this
     */
    protected function disableExceptionHandling()
    {
        app()->instance(Handler::class, new class extends Handler {
            public function __construct()
            {
            }


            public function report(Exception $e)
            {
            }


            public function render($request, Exception $e)
            {
                throw $e;
            }
        });

        return $this;
    }
}
