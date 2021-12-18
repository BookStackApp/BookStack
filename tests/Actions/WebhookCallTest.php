<?php

namespace Tests\Actions;

use BookStack\Actions\ActivityLogger;
use BookStack\Actions\ActivityType;
use BookStack\Actions\DispatchWebhookJob;
use BookStack\Actions\Webhook;
use BookStack\Auth\User;
use BookStack\Entities\Models\Page;
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

    public function test_failed_webhook_call_logs_error()
    {
        $logger = $this->withTestLogger();
        Http::fake([
            '*' => Http::response('', 500),
        ]);
        $this->newWebhook(['active' => true, 'endpoint' => 'https://wh.example.com'], ['all']);

        $this->runEvent(ActivityType::ROLE_CREATE);

        $this->assertTrue($logger->hasError('Webhook call to endpoint https://wh.example.com failed with status 500'));
    }

    public function test_webhook_call_data_format()
    {
        Http::fake([
            '*' => Http::response('', 200),
        ]);
        $webhook = $this->newWebhook(['active' => true, 'endpoint' => 'https://wh.example.com'], ['all']);
        /** @var Page $page */
        $page = Page::query()->first();
        $editor = $this->getEditor();

        $this->runEvent(ActivityType::PAGE_UPDATE, $page, $editor);

        Http::assertSent(function(Request $request) use ($editor, $page, $webhook) {
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
            $user = $this->getEditor();
        }

        $this->actingAs($user);

        $activityLogger = $this->app->make(ActivityLogger::class);
        $activityLogger->add($event, $detail);
    }

    protected function newWebhook(array $attrs = [], array $events = ['all']): Webhook
    {
        /** @var Webhook $webhook */
        $webhook = Webhook::factory()->create($attrs);

        foreach ($events as $event) {
            $webhook->trackedEvents()->create(['event' => $event]);
        }

        return $webhook;
    }

}