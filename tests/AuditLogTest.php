<?php

namespace Tests;

use BookStack\Actions\Activity;
use BookStack\Actions\ActivityService;
use BookStack\Actions\ActivityType;
use BookStack\Auth\UserRepo;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Entities\Tools\TrashCan;
use Carbon\Carbon;

class AuditLogTest extends TestCase
{
    /** @var ActivityService */
    protected $activityService;

    public function setUp(): void
    {
        parent::setUp();
        $this->activityService = app(ActivityService::class);
    }

    public function test_only_accessible_with_right_permissions()
    {
        $viewer = $this->getViewer();
        $this->actingAs($viewer);

        $resp = $this->get('/settings/audit');
        $this->assertPermissionError($resp);

        $this->giveUserPermissions($viewer, ['settings-manage']);
        $resp = $this->get('/settings/audit');
        $this->assertPermissionError($resp);

        $this->giveUserPermissions($viewer, ['users-manage']);
        $resp = $this->get('/settings/audit');
        $resp->assertStatus(200);
        $resp->assertSeeText('Audit Log');
    }

    public function test_shows_activity()
    {
        $admin = $this->getAdmin();
        $this->actingAs($admin);
        $page = Page::query()->first();
        $this->activityService->addForEntity($page, ActivityType::PAGE_CREATE);
        $activity = Activity::query()->orderBy('id', 'desc')->first();

        $resp = $this->get('settings/audit');
        $resp->assertSeeText($page->name);
        $resp->assertSeeText('page_create');
        $resp->assertSeeText($activity->created_at->toDateTimeString());
        $resp->assertElementContains('.table-user-item', $admin->name);
    }

    public function test_shows_name_for_deleted_items()
    {
        $this->actingAs($this->getAdmin());
        $page = Page::query()->first();
        $pageName = $page->name;
        $this->activityService->addForEntity($page, ActivityType::PAGE_CREATE);

        app(PageRepo::class)->destroy($page);
        app(TrashCan::class)->empty();

        $resp = $this->get('settings/audit');
        $resp->assertSeeText('Deleted Item');
        $resp->assertSeeText('Name: ' . $pageName);
    }

    public function test_shows_activity_for_deleted_users()
    {
        $viewer = $this->getViewer();
        $this->actingAs($viewer);
        $page = Page::query()->first();
        $this->activityService->addForEntity($page, ActivityType::PAGE_CREATE);

        $this->actingAs($this->getAdmin());
        app(UserRepo::class)->destroy($viewer);

        $resp = $this->get('settings/audit');
        $resp->assertSeeText("[ID: {$viewer->id}] Deleted User");
    }

    public function test_filters_by_key()
    {
        $this->actingAs($this->getAdmin());
        $page = Page::query()->first();
        $this->activityService->addForEntity($page, ActivityType::PAGE_CREATE);

        $resp = $this->get('settings/audit');
        $resp->assertSeeText($page->name);

        $resp = $this->get('settings/audit?event=page_delete');
        $resp->assertDontSeeText($page->name);
    }

    public function test_date_filters()
    {
        $this->actingAs($this->getAdmin());
        $page = Page::query()->first();
        $this->activityService->addForEntity($page, ActivityType::PAGE_CREATE);

        $yesterday = (Carbon::now()->subDay()->format('Y-m-d'));
        $tomorrow = (Carbon::now()->addDay()->format('Y-m-d'));

        $resp = $this->get('settings/audit?date_from=' . $yesterday);
        $resp->assertSeeText($page->name);

        $resp = $this->get('settings/audit?date_from=' . $tomorrow);
        $resp->assertDontSeeText($page->name);

        $resp = $this->get('settings/audit?date_to=' . $tomorrow);
        $resp->assertSeeText($page->name);

        $resp = $this->get('settings/audit?date_to=' . $yesterday);
        $resp->assertDontSeeText($page->name);
    }

    public function test_user_filter()
    {
        $admin = $this->getAdmin();
        $editor = $this->getEditor();
        $this->actingAs($admin);
        $page = Page::query()->first();
        $this->activityService->addForEntity($page, ActivityType::PAGE_CREATE);

        $this->actingAs($editor);
        $chapter = Chapter::query()->first();
        $this->activityService->addForEntity($chapter, ActivityType::CHAPTER_UPDATE);

        $resp = $this->actingAs($admin)->get('settings/audit?user=' . $admin->id);
        $resp->assertSeeText($page->name);
        $resp->assertDontSeeText($chapter->name);

        $resp = $this->actingAs($admin)->get('settings/audit?user=' . $editor->id);
        $resp->assertSeeText($chapter->name);
        $resp->assertDontSeeText($page->name);
    }

    public function test_ip_address_logged_and_visible()
    {
        config()->set('app.proxies', '*');
        $editor = $this->getEditor();
        /** @var Page $page */
        $page = Page::query()->first();

        $this->actingAs($editor)->put($page->getUrl(), [
            'name' => 'Updated page',
            'html' => '<p>Updated content</p>',
        ], [
            'X-Forwarded-For' => '192.123.45.1',
        ])->assertRedirect($page->refresh()->getUrl());

        $this->assertDatabaseHas('activities', [
            'type'      => ActivityType::PAGE_UPDATE,
            'ip'        => '192.123.45.1',
            'user_id'   => $editor->id,
            'entity_id' => $page->id,
        ]);

        $resp = $this->asAdmin()->get('/settings/audit');
        $resp->assertSee('192.123.45.1');
    }

    public function test_ip_address_not_logged_in_demo_mode()
    {
        config()->set('app.proxies', '*');
        config()->set('app.env', 'demo');
        $editor = $this->getEditor();
        /** @var Page $page */
        $page = Page::query()->first();

        $this->actingAs($editor)->put($page->getUrl(), [
            'name' => 'Updated page',
            'html' => '<p>Updated content</p>',
        ], [
            'X-Forwarded-For' => '192.123.45.1',
            'REMOTE_ADDR'     => '192.123.45.2',
        ])->assertRedirect($page->refresh()->getUrl());

        $this->assertDatabaseHas('activities', [
            'type'      => ActivityType::PAGE_UPDATE,
            'ip'        => '127.0.0.1',
            'user_id'   => $editor->id,
            'entity_id' => $page->id,
        ]);
    }
}
