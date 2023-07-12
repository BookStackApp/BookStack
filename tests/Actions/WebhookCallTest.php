<?php

namespace Tests\Actions;

use BookStack\Activity\ActivityType;
use BookStack\Activity\DispatchWebhookJob;
use BookStack\Activity\Models\Webhook;
use BookStack\Activity\Tools\ActivityLogger;
use BookStack\Api\ApiToken;
use BookStack\Entities\Models\PageRevision;
use BookStack\Users\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
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
        $this->newWebhook(['active' => true, 'endpoint' => 'https://wh.example.com'], ['all']);
        Http::fake([
            '*' => Http::response('', 500),
        ]);

        $user = $this->users->newUser();
        $resp = $this->asAdmin()->delete($user->getEditUrl());
        $resp->assertRedirect('/settings/users');

        /** @var ApiToken $apiToken */
        $editor = $this->users->editor();
        $apiToken = ApiToken::factory()->create(['user_id' => $editor]);
        $resp = $this->delete($editor->getEditUrl('/api-tokens/' . $apiToken->id));
        $resp->assertRedirect($editor->getEditUrl('#api_tokens'));
    }

    public function test_failed_webhook_call_logs_error()
    {
        $logger = $this->withTestLogger();
        Http::fake([
            '*' => Http::response('', 500),
        ]);
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
        Http::shouldReceive('asJson')->andThrow(new \Exception('Failed to perform request'));

        $logger = $this->withTestLogger();
        $webhook = $this->newWebhook(['active' => true, 'endpoint' => 'https://wh.example.com'], ['all']);
        $this->assertNull($webhook->last_errored_at);

        $this->runEvent(ActivityType::ROLE_CREATE);

        $this->assertTrue($logger->hasError('Webhook call to endpoint https://wh.example.com failed with error "Failed to perform request"'));

        $webhook->refresh();
        $this->assertEquals('Failed to perform request', $webhook->last_error);
        $this->assertNotNull($webhook->last_errored_at);
    }

    public function test_webhook_call_data_format()
    {
        Http::fake([
            '*' => Http::response('', 200),
        ]);
        $webhook = $this->newWebhook(['active' => true, 'endpoint' => 'https://wh.example.com'], ['all']);
        $page = $this->entities->page();
        $editor = $this->users->editor();

        $this->runEvent(ActivityType::PAGE_UPDATE, $page, $editor);

        Http::assertSent(function (Request $request) use ($editor, $page, $webhook) {
            $reqData = $request->data();

            return $request->isJson()
                && $reqData['event'] === 'page_update'
                && $reqData['text'] === ($editor->name . ' updated page "' . $page->name . '"')
                && is_string($reqData['triggered_at'])
                && $reqData['triggered_by']['name'] === $editor->name
                && $reqData['triggered_by_profile_url'] === $editor->getProfileUrl()
                && $reqData['webhook_id'] === $webhook->id
                && $reqData['webhook_name'] === $webhook->name
                && $reqData['url'] === $page->getUrl()
                && $reqData['related_item']['name'] === $page->name;
        });
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
