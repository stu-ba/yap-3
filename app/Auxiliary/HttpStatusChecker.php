<?php

namespace Yap\Auxiliary;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

abstract class HttpStatusChecker
{

    protected $httpClient;

    protected $cache;

    protected $url;


    /**
     * @param string $url
     *
     * @return \Yap\Auxiliary\HttpStatusChecker
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }


    /**
     * @param int $statusCode
     *
     * @return bool
     */
    public function checkCached(int $statusCode = 200): bool
    {
        return $this->getCache()->remember(__CLASS__.$statusCode, 5, function () use ($statusCode) {
            return $this->check($statusCode);
        });
    }


    protected function getCache()
    {
        if (is_null($this->cache)) {
            $this->cache = cache();
        }

        return $this->cache;
    }


    /**
     * @param int $statusCode
     *
     * @return bool
     */
    public function check(int $statusCode = 200): bool
    {
        $response = $this->getResponse();
        return ! is_null($response) && $response->getStatusCode() === $statusCode;
    }


    /**
     * @return null|\Psr\Http\Message\ResponseInterface
     */
    public function getResponse(): ?\Psr\Http\Message\ResponseInterface
    {
        try {
            return $this->getHttpClient()->get($this->url, [
                'timeout' => '2',
                'headers' => [
                    'User-Agent' => 'yap/1.0',
                ],
            ]);
        } catch (RequestException $exception) {
            return null;
        }
    }


    /**
     * Get a instance of the Guzzle HTTP client.
     *
     * @return \GuzzleHttp\Client
     */
    protected function getHttpClient(): \GuzzleHttp\Client
    {
        if (is_null($this->httpClient)) {
            $this->httpClient = new Client();
        }

        return $this->httpClient;
    }
}