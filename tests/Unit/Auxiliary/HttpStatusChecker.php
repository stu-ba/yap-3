<?php

namespace Tests\Unit\Auxiliary;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

class HttpStatusChecker extends TestCase
{

    /**@var $service \Tests\Unit\Auxiliary\Service */
    protected $service;


    public function setUp()
    {
        parent::setUp();

        $clientMock = new MockHandler([
            new Response(200),
            new RequestException('Nothing here', new Request('GET', str_random())),
        ]);

        $handler = HandlerStack::create($clientMock);
        $client  = new Client(['handler' => $handler]);

        $this->service = new Service();
        $this->service->setHttpClient($client);
    }


    public function testChecker()
    {
        $this->assertTrue($this->service->check(), 'Check that response is 200');
        $this->assertFalse($this->service->check(),
            'Check that response throws RequestException, resulting in unreachable (false)');
    }
}

class Service extends \Yap\Auxiliary\HttpStatusChecker
{

    /**
     * For testing purposes only.
     *
     * @param null $client
     *
     * @return self
     */
    public function setHttpClient($client = null): self
    {
        if ( ! is_null($client)) {
            $this->httpClient = $client;
        }

        return $this;
    }
}