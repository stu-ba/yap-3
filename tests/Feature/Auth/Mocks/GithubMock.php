<?php

namespace Tests\Feature\Auth\Mocks;

use Laravel\Socialite\Contracts\Factory as SocialiteOriginal;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Faker\Factory;

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

        $socialiteUser = $this->mockSocialiteUser($attributes);

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


    /**
     * @param array $attributes
     *
     * @return mixed
     */
    protected function mockSocialiteUser(array $attributes)
    {
        $socialiteUser = $this->createMock(User::class);
        foreach ($attributes as $attribute => $value) {
            $socialiteUser->{$attribute} = $value;
            if ( ! in_array($attribute, ['user', 'token'])) {
                $socialiteUser->expects($this->any())->method('get'.$attribute)->willReturn($value);
            }
        }

        return $socialiteUser;
    }

    /**
     *
     * @return array
     */
    private function dummyGithubUserData(): array
    {
        $faker = Factory::create();
        $data = [
            'token'    => str_random(32),
            'id'       => rand(4, 32),
            'email'    => $faker->safeEmail,
            'nickname' => $faker->userName,
            'name'     => $faker->firstName.' '.$faker->lastName,
            'avatar'   => $faker->imageUrl(),
            'user'     => ['bio' => 'abc']
        ];

        return $data;
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    private function transformUserData($data)
    {
        $data['github_id'] = $data['id'];
        $data['username'] = $data['nickname'];
        $data['bio'] = $data['user']['bio'];
        unset($data['id'], $data['nickname'], $data['user'], $data['token']);

        return $data;
    }

    /**
     * @return array
     */
    private function generateDummyUserDataAndGithubUser(): array
    {
        $githubUserData = $this->dummyGithubUserData();
        $githubUser = $this->mockSocialiteUser($githubUserData);
        $userData = $this->transformUserData($githubUserData);

        return [$githubUser, $userData];
    }
}