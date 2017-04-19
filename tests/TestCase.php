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
            $this->forceSyncQueueDriver();
        }

        if (isset($uses[ForceArrayMailerDriver::class])) {
            $this->forceArrayMailerDriver();
        }
    }


    /**
     * Make post Json request with XMLHttp header.
     *
     * @param  string $uri
     * @param  array  $data
     * @param  array  $headers
     *
     * @return $this
     */
    protected function postXMLHttp(string $uri, array $data, $headers = [])
    {
        return $this->postJson($uri, $data, array_merge($headers, ['X-Requested-With' => 'XMLHttpRequest']));
    }


    protected function assertStatus(int $status)
    {
        return $this->assertResponseStatus($status);
    }


    protected function assertJsonArray(array $data, $message = '')
    {
        return $this->assertJson(json_encode($data), $message);
    }


    /**
     * Disable Laravel's exception handling.
     *
     * @return $this
     */
    protected function disableExceptionHandling()
    {
        app()->instance(Handler::class, new class() extends Handler
        {

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
