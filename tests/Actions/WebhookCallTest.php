<?php

namespace Tests\Actions;

use BookStack\Activity\ActivityType;
use BookStack\Activity\DispatchWebhookJob;
use BookStack\Activity\Models\Webhook;
use BookStack\Activity\Tools\ActivityLogger;
use BookStack\Api\ApiToken;
use BookStack\Users\Models\User;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class WebhookCallTest extends TestCase
{
    public function test_webhook_listening_to_all_called_on_event()
    {
        $this->newWebhook([], ['all']);
        Bus::fake();
        $this->runEvent(ActivityType::ROLE_CREATE);
        Bus::assertDispatched(DispatchWebhookJob::class);
    }

    public function test_webhook_listening_to_specific_event_called_on_event()
    {
        $this->newWebhook([], [ActivityType::ROLE_UPDATE]);
        Bus::fake();
        $this->runEvent(ActivityType::ROLE_UPDATE);
        Bus::assertDispatched(DispatchWebhookJob::class);
    }

    public function test_webhook_listening_to_specific_event_not_called_on_other_event()
    {
        $this->newWebhook([], [ActivityType::ROLE_UPDATE]);
        Bus::fake();
        $this->runEvent(ActivityType::ROLE_CREATE);
        Bus::assertNotDispatched(DispatchWebhookJob::class);
    }

    public function test_inactive_webhook_not_called_on_event()
    {
        $this->newWebhook(['active' => false], ['all']);
        Bus::fake();
        $this->runEvent(ActivityType::ROLE_CREATE);
        Bus::assertNotDispatched(DispatchWebhookJob::class);
    }

    public function test_webhook_runs_for_delete_actions()
    {
        // This test must not fake the queue/bus since this covers an issue
        // around handling and serialization of items now deleted from the database.
        $webhook = $this->newWebhook(['active' => true, 'endpoint' => 'https://wh.example.com'], ['all']);
        $this->mockHttpClient([new Response(500)]);

        $user = $this->users->newUser();
        $resp = $this->asAdmin()->delete($user->getEditUrl());
        $resp->assertRedirect('/settings/users');

        /** @var ApiToken $apiToken */
        $editor = $this->users->editor();
        $apiToken = ApiToken::factory()->create(['user_id' => $editor]);
        $this->delete($apiToken->getUrl())->assertRedirect();

        $webhook->refresh();
        $this->assertEquals('Response status from endpoint was 500', $webhook->last_error);
    }

    public function test_failed_webhook_call_logs_error()
    {
        $logger = $this->withTestLogger();
        $this->mockHttpClient([new Response(500)]);
        $webhook = $this->newWebhook(['active' => true, 'endpoint' => 'https://wh.example.com'], ['all']);
        $this->assertNull($webhook->last_errored_at);

        $this->runEvent(ActivityType::ROLE_CREATE);

        $this->assertTrue($logger->hasError('Webhook call to endpoint https://wh.example.com failed with status 500'));

        $webhook->refresh();
        $this->assertEquals('Response status from endpoint was 500', $webhook->last_error);
        $this->assertNotNull($webhook->last_errored_at);
    }

    public function test_webhook_call_exception_is_caught_and_logged()
    {
        $this->mockHttpClient([new ConnectException('Failed to perform request', new \GuzzleHttp\Psr7\Request('GET', ''))]);

        $logger = $this->withTestLogger();
        $webhook = $this->newWebhook(['active' => true, 'endpoint' => 'https://wh.example.com'], ['all']);
        $this->assertNull($webhook->last_errored_at);

        $this->runEvent(ActivityType::ROLE_CREATE);

        $this->assertTrue($logger->hasError('Webhook call to endpoint https://wh.example.com failed with error "Failed to perform request"'));

        $webhook->refresh();
        $this->assertEquals('Failed to perform request', $webhook->last_error);
        $this->assertNotNull($webhook->last_errored_at);
    }

    public function test_webhook_uses_ssr_hosts_option_if_set()
    {
        config()->set('app.ssr_hosts', 'https://*.example.com');
        $responses = $this->mockHttpClient();

        $webhook = $this->newWebhook(['active' => true, 'endpoint' => 'https://wh.example.co.uk'], ['all']);
        $this->runEvent(ActivityType::ROLE_CREATE);
        $this->assertEquals(0, $responses->requestCount());

        $webhook->refresh();
        $this->assertEquals('The URL does not match the configured allowed SSR hosts', $webhook->last_error);
        $this->assertNotNull($webhook->last_errored_at);
    }

    public function test_webhook_call_data_format()
    {
        $responses = $this->mockHttpClient([new Response(200, [], '')]);
        $webhook = $this->newWebhook(['active' => true, 'endpoint' => 'https://wh.example.com'], ['all']);
        $page = $this->entities->page();
        $editor = $this->users->editor();

        $this->runEvent(ActivityType::PAGE_UPDATE, $page, $editor);

        $request = $responses->latestRequest();
        $reqData = json_decode($request->getBody(), true);
        $this->assertEquals('page_update', $reqData['event']);
        $this->assertEquals(($editor->name . ' updated page "' . $page->name . '"'), $reqData['text']);
        $this->assertIsString($reqData['triggered_at']);
        $this->assertEquals($editor->name, $reqData['triggered_by']['name']);
        $this->assertEquals($editor->getProfileUrl(), $reqData['triggered_by_profile_url']);
        $this->assertEquals($webhook->id, $reqData['webhook_id']);
        $this->assertEquals($webhook->name, $reqData['webhook_name']);
        $this->assertEquals($page->getUrl(), $reqData['url']);
        $this->assertEquals($page->name, $reqData['related_item']['name']);
    }

    protected function runEvent(string $event, $detail = '', ?User $user = null)
    {
        if (is_null($user)) {
            $user = $this->users->editor();
        }

        $this->actingAs($user);

        $activityLogger = $this->app->make(ActivityLogger::class);
        $activityLogger->add($event, $detail);
    }

    protected function newWebhook(array $attrs, array $events): Webhook
    {
        /** @var Webhook $webhook */
        $webhook = Webhook::factory()->create($attrs);

        foreach ($events as $event) {
            $webhook->trackedEvents()->create(['event' => $event]);
        }

        return $webhook;
    }
}
