<?php

namespace Tests\Actions;

use BookStack\Actions\ActivityType;
use BookStack\Actions\Webhook;
use Tests\TestCase;

class WebhookManagementTest extends TestCase
{

    public function test_index_view()
    {
        $webhook = $this->newWebhook([
            'name' => 'My awesome webhook',
            'endpoint' => 'https://example.com/donkey/webhook',
        ], ['all']);

        $resp = $this->asAdmin()->get('/settings/webhooks');
        $resp->assertOk();
        $resp->assertElementContains('a[href$="/settings/webhooks/create"]', 'Create New Webhook');
        $resp->assertElementExists('a[href="' . $webhook->getUrl() . '"]', $webhook->name);
        $resp->assertSee($webhook->endpoint);
        $resp->assertSee('All system events');
    }

    public function test_create_view()
    {
        $resp = $this->asAdmin()->get('/settings/webhooks/create');
        $resp->assertOk();
        $resp->assertSee('Create New Webhook');
        $resp->assertElementContains('form[action$="/settings/webhooks/create"] button', 'Save Webhook');
    }

    public function test_store()
    {
        $resp = $this->asAdmin()->post('/settings/webhooks/create', [
            'name' => 'My first webhook',
            'endpoint' => 'https://example.com/webhook',
            'events' => ['all'],
        ]);

        $resp->assertRedirect('/settings/webhooks');
        $this->assertActivityExists(ActivityType::WEBHOOK_CREATE);

        $resp = $this->followRedirects($resp);
        $resp->assertSee('Webhook successfully created');

        $this->assertDatabaseHas('webhooks', [
            'name' => 'My first webhook',
            'endpoint' => 'https://example.com/webhook',
        ]);

        /** @var Webhook $webhook */
        $webhook = Webhook::query()->where('name', '=', 'My first webhook')->first();
        $this->assertDatabaseHas('webhook_tracked_events', [
            'webhook_id' => $webhook->id,
            'event' => 'all',
        ]);
    }

    public function test_edit_view()
    {
        $webhook = $this->newWebhook();

        $resp = $this->asAdmin()->get('/settings/webhooks/' . $webhook->id);
        $resp->assertOk();
        $resp->assertSee('Edit Webhook');
        $resp->assertElementContains('form[action="' . $webhook->getUrl() . '"] button', 'Save Webhook');
        $resp->assertElementContains('a[href="' . $webhook->getUrl('/delete') . '"]', 'Delete Webhook');
        $resp->assertElementExists('input[type="checkbox"][value="all"][name="events[]"]');
    }

    public function test_update()
    {
        $webhook = $this->newWebhook();

        $resp = $this->asAdmin()->put('/settings/webhooks/' . $webhook->id, [
            'name' => 'My updated webhook',
            'endpoint' => 'https://example.com/updated-webhook',
            'events' => [ActivityType::PAGE_CREATE, ActivityType::PAGE_UPDATE],
        ]);
        $resp->assertRedirect('/settings/webhooks');

        $resp = $this->followRedirects($resp);
        $resp->assertSee('Webhook successfully updated');

        $this->assertDatabaseHas('webhooks', [
            'id' => $webhook->id,
            'name' => 'My updated webhook',
            'endpoint' => 'https://example.com/updated-webhook',
        ]);

        $trackedEvents = $webhook->trackedEvents()->get();
        $this->assertCount(2, $trackedEvents);
        $this->assertEquals(['page_create', 'page_update'], $trackedEvents->pluck('event')->values()->all());

        $this->assertActivityExists(ActivityType::WEBHOOK_UPDATE);
    }

    public function test_delete_view()
    {
        $webhook = $this->newWebhook(['name' => 'Webhook to delete']);

        $resp = $this->asAdmin()->get('/settings/webhooks/' . $webhook->id . '/delete');
        $resp->assertOk();
        $resp->assertSee('Delete Webhook');
        $resp->assertSee('This will fully delete this webhook, with the name \'Webhook to delete\', from the system.');
        $resp->assertElementContains('form[action$="/settings/webhooks/' . $webhook->id . '"]', 'Delete');
    }

    public function test_destroy()
    {
        $webhook = $this->newWebhook();

        $resp = $this->asAdmin()->delete('/settings/webhooks/' . $webhook->id);
        $resp->assertRedirect('/settings/webhooks');

        $resp = $this->followRedirects($resp);
        $resp->assertSee('Webhook successfully deleted');

        $this->assertDatabaseMissing('webhooks', ['id' => $webhook->id]);
        $this->assertDatabaseMissing('webhook_tracked_events', ['webhook_id' => $webhook->id]);

        $this->assertActivityExists(ActivityType::WEBHOOK_DELETE);
    }

    public function test_settings_manage_permission_required_for_webhook_routes()
    {
        $editor = $this->getEditor();
        $this->actingAs($editor);

        $routes = [
            ['GET', '/settings/webhooks'],
            ['GET', '/settings/webhooks/create'],
            ['POST', '/settings/webhooks/create'],
            ['GET', '/settings/webhooks/1'],
            ['PUT', '/settings/webhooks/1'],
            ['DELETE', '/settings/webhooks/1'],
            ['GET', '/settings/webhooks/1/delete'],
        ];

        foreach ($routes as [$method, $endpoint]) {
            $resp = $this->call($method, $endpoint);
            $this->assertPermissionError($resp);
        }

        $this->giveUserPermissions($editor, ['settings-manage']);

        foreach ($routes as [$method, $endpoint]) {
            $resp = $this->call($method, $endpoint);
            $this->assertNotPermissionError($resp);
        }
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