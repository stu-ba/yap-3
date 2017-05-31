<?php

namespace Yap\Auxiliary\HttpCheckers;

interface Contract
{

    function setUrl(string $url);


    /**
     * Check if url returns given status code.
     *
     * @param int $statusCode
     *
     * @return bool
     */
    function check(int $statusCode = 200): bool;


    /**
     * @param int $statusCode
     *
     * @return bool
     */
    function cached(int $statusCode = 200): bool;


    /**
     * Get cache instance.
     *
     * @return mixed
     */
    function getCache();


    /**
     * Generate key for cache.
     *
     * @param int $statusCode
     *
     * @return string
     */
    function cacheKey(int $statusCode): string;


    /**
     * @param int $statusCode
     *
     * @return bool
     */
    public function live(int $statusCode = 200): bool;


    /**
     * @return null|\Psr\Http\Message\ResponseInterface
     */
    public function getResponse(): ?\Psr\Http\Message\ResponseInterface;


    /**
     * Get a instance of the Guzzle HTTP client.
     *
     * @return \GuzzleHttp\Client
     */
    function getHttpClient(): \GuzzleHttp\Client;


    /**
     *
     * @param int $statusCode
     */
    public function forgetCached(int $statusCode = 200): void;
}