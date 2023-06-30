<?php

namespace Tests\Actions;

use BookStack\Activity\ActivityType;
use BookStack\Activity\Models\Webhook;
use BookStack\Activity\Tools\WebhookFormatter;
use Illuminate\Support\Arr;
use Tests\TestCase;

class WebhookFormatTesting extends TestCase
{
    public function test_entity_events_show_related_user_info()
    {
        $events = [
            ActivityType::BOOK_UPDATE    => $this->entities->book(),
            ActivityType::CHAPTER_CREATE => $this->entities->chapter(),
            ActivityType::PAGE_MOVE      => $this->entities->page(),
        ];

        foreach ($events as $event => $entity) {
            $data = $this->getWebhookData($event, $entity);

            $this->assertEquals($entity->createdBy->name, Arr::get($data, 'related_item.created_by.name'));
            $this->assertEquals($entity->updatedBy->id, Arr::get($data, 'related_item.updated_by.id'));
            $this->assertEquals($entity->ownedBy->slug, Arr::get($data, 'related_item.owned_by.slug'));
        }
    }

    public function test_page_create_and_update_events_show_revision_info()
    {
        $page = $this->entities->page();
        $this->asEditor()->put($page->getUrl(), ['name' => 'Updated page', 'html' => 'new page html', 'summary' => 'Update a']);

        $data = $this->getWebhookData(ActivityType::PAGE_UPDATE, $page);
        $this->assertEquals($page->currentRevision->id, Arr::get($data, 'related_item.current_revision.id'));
        $this->assertEquals($page->currentRevision->type, Arr::get($data, 'related_item.current_revision.type'));
        $this->assertEquals('Update a', Arr::get($data, 'related_item.current_revision.summary'));
    }

    protected function getWebhookData(string $event, $detail): array
    {
        $webhook = Webhook::factory()->make();
        $user = $this->users->editor();
        $formatter = WebhookFormatter::getDefault($event, $webhook, $detail, $user, time());

        return $formatter->format();
    }
}
