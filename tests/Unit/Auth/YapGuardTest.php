<?php

namespace Tests\Unit\Auth;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Kyslik\Django\Signing\Signer;
use Mockery;
use Tests\TestCase;
use Yap\Foundation\Auth\YapGuard;

class AuthTokenGuardTest extends TestCase
{

    /**
     * @var Signer
     */
    protected $signer;

    /**
     * @var array
     */
    protected $credentials;


    public function setUp()
    {
        parent::setUp();
        $this->signer = resolve(Signer::class);
        $this->credentials = ['id' => -1, 'username' => 'neo'];
    }


    public function tearDown()
    {
        Mockery::close();
    }


    public function testUserCanBeRetrievedByBearerToken()
    {
        $token = $this->signer->dumps($this->credentials);
        $provider = Mockery::mock(UserProvider::class);
        $provider->shouldReceive('retrieveByCredentials')->once()->with($this->credentials)->andReturn((object)$this->credentials);
        $request = Request::create('/', 'GET', [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer '.$token]);

        $guard = new YapGuard($provider, $request, $this->signer);

        $user = $guard->user();

        $this->assertEquals(-1, $user->id);
        $this->assertEquals('neo', $user->username);
    }


    public function testGetCredentialsSucceeds()
    {
        $token = $this->signer->dumps($this->credentials);
        $provider = Mockery::mock(UserProvider::class);
        $request = Request::create('/', 'GET', [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer '.$token]);

        $guard = new YapGuard($provider, $request, $this->signer);

        $this->assertEquals($this->credentials, $guard->getCredentials());
    }


    public function testGetCredentialsWhenTokenIsExpired()
    {
        $token = $this->signer->setTimestamp(time() - 99)->dumps($this->credentials);

        $provider = Mockery::mock(UserProvider::class);
        $request = Request::create('/', 'GET', [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer '.$token]);

        $this->signer->setMaxAge(10);
        $guard = new YapGuard($provider, $request, $this->signer);

        $this->assertEmpty($guard->getCredentials());
    }


    public function testGetCredentialsFails()
    {
        $provider = Mockery::mock(UserProvider::class);
        $request = Request::create('/', 'GET', [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer abc']);

        $guard = new YapGuard($provider, $request, $this->signer);

        $this->assertEmpty($guard->getCredentials());
    }


    public function testValidateCanDetermineIfCredentialsAreValid()
    {
        $provider = Mockery::mock(UserProvider::class);
        $user = new AuthTokenGuardTestUser;

        $provider->shouldReceive('retrieveByCredentials')->once()->with($this->credentials)->andReturn($user);
        $request = Request::create('/', 'GET', [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer abc']);

        $guard = new YapGuard($provider, $request, $this->signer);

        $this->assertTrue($guard->validate($this->credentials));
    }


    public function testValidateCanDetermineIfCredentialsAreInvalid()
    {
        $provider = Mockery::mock(UserProvider::class);
        $provider->shouldReceive('retrieveByCredentials')->once()->with($this->credentials)->andReturn(null);
        $request = Request::create('/', 'GET', [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer abc']);

        $guard = new YapGuard($provider, $request, $this->signer);

        $this->assertFalse($guard->validate($this->credentials));
    }


    public function testValidateIfApiTokenIsInvalid()
    {
        $provider = Mockery::mock(UserProvider::class);
        $request = Request::create('/', 'GET', [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer abc']);

        $guard = new YapGuard($provider, $request, $this->signer);

        $this->assertFalse($guard->validate([]));
    }
}

class AuthTokenGuardTestUser
{

    public $id;

    public $username;


    public function getAuthIdentifier()
    {
        return $this->id;
    }
}