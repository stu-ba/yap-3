<?php

namespace Yap\Foundation\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Kyslik\Django\Signing\Exceptions\BadSignatureException;
use Kyslik\Django\Signing\Signer;

class TokenGuard implements Guard
{
    use GuardHelpers;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The request instance.
     *
     * @var array
     */
    protected $storageFields;

    /**
     * Signer instance
     *
     * @var Signer
     */
    protected $signer;


    /**
     * Create a new authentication guard.
     *
     * @param  \Illuminate\Contracts\Auth\UserProvider $provider
     * @param  \Illuminate\Http\Request                $request
     * @param Signer                                   $signer
     */
    public function __construct(UserProvider $provider, Request $request, Signer $signer)
    {
        $this->request = $request;
        $this->provider = $provider;
        $this->signer = $signer;
        $this->storageFields = ['id', 'username'];
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (! is_null($this->user)) {
            return $this->user;
        }

        $user = null;

        $token = $this->getTokenForRequest();

        if (! empty($token)) {
            $user = $this->provider->retrieveByCredentials(
                $this->getCredentialsForToken($token)
            );
        }

        return $this->user = $user;
    }

    /**
     * Get the token for the current request.
     *
     * @return string
     */
    public function getTokenForRequest()
    {
        return $this->request->bearerToken();
    }

    public function getCredentialsForToken(string $token): array {

        try {
            $credentials = $this->signer->loads($token);
        } catch (BadSignatureException $exception) {
            return [];
        }

        foreach ($this->storageFields as $field) {
            if (!array_key_exists($field, $credentials)) {
                return [];
            }
        }

        return array_only($credentials, $this->storageFields);
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        foreach ($this->storageFields as $field) {
            if (!array_key_exists($field, $credentials)) {
                return false;
            }
        }

        if ($this->provider->retrieveByCredentials($credentials)) {
            return true;
        }

        return false;
    }

    /**
     * Set the current request instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }
}
