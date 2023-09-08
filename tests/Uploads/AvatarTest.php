<?php

namespace Tests\Uploads;

use BookStack\Exceptions\HttpFetchException;
use BookStack\Uploads\UserAvatars;
use BookStack\Users\Models\User;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
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

    protected function deleteUserImage(User $user): void
    {
        $this->files->deleteAtRelativePath($user->avatar->path);
    }

    public function test_gravatar_fetched_on_user_create()
    {
        $requests = $this->mockHttpClient([new Response(200, ['Content-Type' => 'image/png'], $this->files->pngImageData())]);
        config()->set(['services.disable_services' => false]);
        $user = User::factory()->make();

        $user = $this->createUserRequest($user);
        $this->assertDatabaseHas('images', [
            'type'       => 'user',
            'created_by' => $user->id,
        ]);
        $this->deleteUserImage($user);

        $expectedUri = 'https://www.gravatar.com/avatar/' . md5(strtolower($user->email)) . '?s=500&d=identicon';
        $this->assertEquals($expectedUri, $requests->latestRequest()->getUri());
    }

    public function test_custom_url_used_if_set()
    {
        config()->set([
            'services.disable_services' => false,
            'services.avatar_url'       => 'https://example.com/${email}/${hash}/${size}',
        ]);

        $user = User::factory()->make();
        $url = 'https://example.com/' . urlencode(strtolower($user->email)) . '/' . md5(strtolower($user->email)) . '/500';
        $requests = $this->mockHttpClient([new Response(200, ['Content-Type' => 'image/png'], $this->files->pngImageData())]);

        $user = $this->createUserRequest($user);
        $this->assertEquals($url, $requests->latestRequest()->getUri());
        $this->deleteUserImage($user);
    }

    public function test_avatar_not_fetched_if_no_custom_url_and_services_disabled()
    {
        config()->set(['services.disable_services' => true]);
        $user = User::factory()->make();
        $requests = $this->mockHttpClient([new Response()]);

        $this->createUserRequest($user);

        $this->assertEquals(0, $requests->requestCount());
    }

    public function test_avatar_not_fetched_if_avatar_url_option_set_to_false()
    {
        config()->set([
            'services.disable_services' => false,
            'services.avatar_url'       => false,
        ]);

        $user = User::factory()->make();
        $requests = $this->mockHttpClient([new Response()]);

        $this->createUserRequest($user);

        $this->assertEquals(0, $requests->requestCount());
    }

    public function test_no_failure_but_error_logged_on_failed_avatar_fetch()
    {
        config()->set(['services.disable_services' => false]);

        $this->mockHttpClient([new ConnectException('Failed to connect', new Request('GET', ''))]);

        $logger = $this->withTestLogger();

        $user = User::factory()->make();
        $this->createUserRequest($user);
        $this->assertTrue($logger->hasError('Failed to save user avatar image'));
    }

    public function test_exception_message_on_failed_fetch()
    {
        // set wrong url
        config()->set([
            'services.disable_services' => false,
            'services.avatar_url'       => 'http_malformed_url/${email}/${hash}/${size}',
        ]);

        $user = User::factory()->make();
        $avatar = app()->make(UserAvatars::class);
        $logger = $this->withTestLogger();
        $this->mockHttpClient([new ConnectException('Could not resolve host http_malformed_url', new Request('GET', ''))]);

        $avatar->fetchAndAssignToUser($user);

        $url = 'http_malformed_url/' . urlencode(strtolower($user->email)) . '/' . md5(strtolower($user->email)) . '/500';
        $this->assertTrue($logger->hasError('Failed to save user avatar image'));
        $exception = $logger->getRecords()[0]['context']['exception'];
        $this->assertInstanceOf(HttpFetchException::class, $exception);
        $this->assertEquals('Cannot get image from ' . $url, $exception->getMessage());
        $this->assertEquals('Could not resolve host http_malformed_url', $exception->getPrevious()->getMessage());
    }
}
