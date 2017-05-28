<?php

namespace Yap\Auxiliary\HttpCheckers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

abstract class Checker
{

    protected $httpClient;

    protected $cache;

    protected $url;


    /**
     * @param string $url
     *
     * @return \Yap\Auxiliary\HttpCheckers\Checker
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }


    /**
     * Check if url returns given status code.
     *
     * @param int $statusCode
     *
     * @return bool
     */
    public function check(int $statusCode = 200): bool
    {
        $cached = $this->cached($statusCode);

        if ($cached === false) {
            return false;
        }

        return $this->live($statusCode);
    }


    /**
     * @param int $statusCode
     *
     * @return bool
     */
    public function cached(int $statusCode = 200): bool
    {
        return $this->getCache()->remember($this->cacheKey($statusCode), 5, function () use ($statusCode) {
            return $this->live($statusCode);
        });
    }


    /**
     * Get cache instance.
     *
     * @return mixed
     */
    protected function getCache()
    {
        if (is_null($this->cache)) {
            $this->cache = cache();
        }

        return $this->cache;
    }


    /**
     * Generate key for cache.
     *
     * @param int $statusCode
     *
     * @return string
     */
    private function cacheKey(int $statusCode): string
    {
        return hash('sha256', get_class($this).$statusCode.$this->url);
    }


    /**
     * @param int $statusCode
     *
     * @return bool
     */
    public function live(int $statusCode = 200): bool
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


    /**
     *
     * @param int $statusCode
     */
    public function forgetCached(int $statusCode = 200): void
    {
        $this->getCache()->forget($this->cacheKey($statusCode));
    }
}