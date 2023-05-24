<?php

namespace Tests\Uploads;

use BookStack\Exceptions\HttpFetchException;
use BookStack\Uploads\HttpFetcher;
use BookStack\Users\Models\User;
use Tests\TestCase;

class AvatarTest extends TestCase
{
    protected function createUserRequest($user): User
    {
        $this->asAdmin()->post('/settings/users/create', [
            'name'             => $user->name,
            'email'            => $user->email,
            'password'         => 'testing101',
            'password-confirm' => 'testing101',
        ]);

        return User::query()->where('email', '=', $user->email)->first();
    }

    protected function assertImageFetchFrom(string $url)
    {
        $http = $this->mock(HttpFetcher::class);

        $http->shouldReceive('fetch')
            ->once()->with($url)
            ->andReturn($this->files->pngImageData());
    }

    protected function deleteUserImage(User $user)
    {
        $this->files->deleteAtRelativePath($user->avatar->path);
    }

    public function test_gravatar_fetched_on_user_create()
    {
        config()->set([
            'services.disable_services' => false,
        ]);
        $user = User::factory()->make();
        $this->assertImageFetchFrom('https://www.gravatar.com/avatar/' . md5(strtolower($user->email)) . '?s=500&d=identicon');

        $user = $this->createUserRequest($user);
        $this->assertDatabaseHas('images', [
            'type'       => 'user',
            'created_by' => $user->id,
        ]);
        $this->deleteUserImage($user);
    }

    public function test_custom_url_used_if_set()
    {
        config()->set([
            'services.disable_services' => false,
            'services.avatar_url'       => 'https://example.com/${email}/${hash}/${size}',
        ]);

        $user = User::factory()->make();
        $url = 'https://example.com/' . urlencode(strtolower($user->email)) . '/' . md5(strtolower($user->email)) . '/500';
        $this->assertImageFetchFrom($url);

        $user = $this->createUserRequest($user);
        $this->deleteUserImage($user);
    }

    public function test_avatar_not_fetched_if_no_custom_url_and_services_disabled()
    {
        config()->set([
            'services.disable_services' => true,
        ]);

        $user = User::factory()->make();

        $http = $this->mock(HttpFetcher::class);
        $http->shouldNotReceive('fetch');

        $this->createUserRequest($user);
    }

    public function test_avatar_not_fetched_if_avatar_url_option_set_to_false()
    {
        config()->set([
            'services.disable_services' => false,
            'services.avatar_url'       => false,
        ]);

        $user = User::factory()->make();

        $http = $this->mock(HttpFetcher::class);
        $http->shouldNotReceive('fetch');

        $this->createUserRequest($user);
    }

    public function test_no_failure_but_error_logged_on_failed_avatar_fetch()
    {
        config()->set([
            'services.disable_services' => false,
        ]);

        $http = $this->mock(HttpFetcher::class);
        $http->shouldReceive('fetch')->andThrow(new HttpFetchException());

        $logger = $this->withTestLogger();

        $user = User::factory()->make();
        $this->createUserRequest($user);
        $this->assertTrue($logger->hasError('Failed to save user avatar image'));
    }
}
