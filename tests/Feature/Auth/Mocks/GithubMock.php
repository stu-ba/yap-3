<?php

namespace Tests\Feature\Auth\Mocks;

use Laravel\Socialite\Contracts\Factory as SocialiteOriginal;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

trait GithubMock
{

    /**
     * Mock the Socialite Factory, so we can hijack the OAuth Request.
     *
     * @param array $attributes
     *
     * @return void
     */
    public function mockSocialiteFacade(array $attributes = [])
    {

        $socialiteUser = $this->createMock(User::class);
        foreach ($attributes as $attribute => $value) {
            $socialiteUser->{$attribute} = $value;
            if ( ! in_array($attribute, ['user', 'token'])) {
                $socialiteUser->expects($this->any())->method('get'.$attribute)->willReturn($value);
            }
        }

        $provider = $this->createMock(GithubProvider::class);
        $provider->expects($this->any())->method('user')->willReturn($socialiteUser);

        $stub = $this->createMock(SocialiteOriginal::class);
        $stub->expects($this->any())->method('driver')->willReturn($provider);

        $this->app->instance(SocialiteOriginal::class, $stub);
    }


    public function mockGithubRedirect($token)
    {
        $redirectResponse = RedirectResponse::create($this->buildGithubLoginUrl($token), 302);

        $provider = $this->getMockBuilder(GithubProvider::class)->disableOriginalConstructor()->setMethods([
            'with',
            'scopes',
            'redirect'
        ])->getMock();
        $provider->expects($this->once())->method('with')->will($this->returnSelf());
        $provider->expects($this->once())->method('scopes')->will($this->returnSelf());
        $provider->expects($this->once())->method('redirect')->willReturn($redirectResponse);

        $stub = $this->createMock(SocialiteOriginal::class);
        $stub->expects($this->any())->method('driver')->willReturn($provider);

        $this->app->instance(SocialiteOriginal::class, $stub);
    }


    /**
     * @param string $encryptedToken
     *
     * @return string
     * @internal param $token
     *
     */
    protected function buildGithubLoginUrl(string $encryptedToken): string
    {
        $query = http_build_query([
            'client_id'     => env('GITHUB_CLIENT_ID'),
            'redirect_uri'  => config('services.github.redirect').'/'.$encryptedToken,
            'scope'         => 'user:email',
            'response_type' => 'code',
            'state'         => 'abc123'
        ]);

        return 'https://github.com/login/oauth/authorize?'.$query;
    }
}