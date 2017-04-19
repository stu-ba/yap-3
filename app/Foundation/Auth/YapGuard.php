<?php

namespace Yap\Foundation\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Kyslik\Django\Signing\Exceptions\BadSignatureException;
use Kyslik\Django\Signing\Signer;

class YapGuard implements Guard
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
        $this->signer = $signer->setMaxAge(config('auth.guards.yap.expire', 30));
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
        if ( ! is_null($this->user)) {
            return $this->user;
        }

        return $this->user = $this->provider->retrieveByCredentials($this->getCredentials());
    }


    public function getCredentials(): array
    {

        try {
            $credentials = $this->signer->loads($this->request->bearerToken() ?? '');
        } catch (BadSignatureException $exception) {
            return [];
        }
        finally {
            //signer is singleton so set max age to default value
            $this->signer->setMaxAge(config('django-signer.default_max_age', 60 * 60));
        }

        foreach ($this->storageFields as $field) {
            if ( ! array_key_exists($field, $credentials)) {
                return [];
            }
        }

        return array_only($credentials, $this->storageFields);
    }


    /**
     * Validate a user's credentials.
     *
     * @param  array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        foreach ($this->storageFields as $field) {
            if ( ! array_key_exists($field, $credentials)) {
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
     * @param  \Illuminate\Http\Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }
}
