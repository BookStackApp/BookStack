<?php

namespace Tests\Actions;

use BookStack\Activity\ActivityType;
use BookStack\Activity\Models\Activity;
use BookStack\Activity\Tools\ActivityLogger;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Entities\Tools\TrashCan;
use BookStack\Users\UserRepo;
use Carbon\Carbon;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    protected ActivityLogger $activityService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activityService = app(ActivityLogger::class);
    }

    public function test_only_accessible_with_right_permissions()
    {
        $viewer = $this->users->viewer();
        $this->actingAs($viewer);

        $resp = $this->get('/settings/audit');
        $this->assertPermissionError($resp);

        $this->permissions->grantUserRolePermissions($viewer, ['settings-manage']);
        $resp = $this->get('/settings/audit');
        $this->assertPermissionError($resp);

        $this->permissions->grantUserRolePermissions($viewer, ['users-manage']);
        $resp = $this->get('/settings/audit');
        $resp->assertStatus(200);
        $resp->assertSeeText('Audit Log');
    }

    public function test_shows_activity()
    {
        $admin = $this->users->admin();
        $this->actingAs($admin);
        $page = $this->entities->page();
        $this->activityService->add(ActivityType::PAGE_CREATE, $page);
        $activity = Activity::query()->orderBy('id', 'desc')->first();

        $resp = $this->get('settings/audit');
        $resp->assertSeeText($page->name);
        $resp->assertSeeText('page_create');
        $resp->assertSeeText($activity->created_at->toDateTimeString());
        $this->withHtml($resp)->assertElementContains('a[href*="users/' . $admin->id . '"]', $admin->name);
    }

    public function test_shows_name_for_deleted_items()
    {
        $this->actingAs($this->users->admin());
        $page = $this->entities->page();
        $pageName = $page->name;
        $this->activityService->add(ActivityType::PAGE_CREATE, $page);

        app(PageRepo::class)->destroy($page);
        app(TrashCan::class)->empty();

        $resp = $this->get('settings/audit');
        $resp->assertSeeText('Deleted Item');
        $resp->assertSeeText('Name: ' . $pageName);
    }

    public function test_shows_activity_for_deleted_users()
    {
        $viewer = $this->users->viewer();
        $this->actingAs($viewer);
        $page = $this->entities->page();
        $this->activityService->add(ActivityType::PAGE_CREATE, $page);

        $this->actingAs($this->users->admin());
        app(UserRepo::class)->destroy($viewer);

        $resp = $this->get('settings/audit');
        $resp->assertSeeText("[ID: {$viewer->id}] Deleted User");
    }

    public function test_filters_by_key()
    {
        $this->actingAs($this->users->admin());
        $page = $this->entities->page();
        $this->activityService->add(ActivityType::PAGE_CREATE, $page);

        $resp = $this->get('settings/audit');
        $resp->assertSeeText($page->name);

        $resp = $this->get('settings/audit?event=page_delete');
        $resp->assertDontSeeText($page->name);
    }

    public function test_date_filters()
    {
        $this->actingAs($this->users->admin());
        $page = $this->entities->page();
        $this->activityService->add(ActivityType::PAGE_CREATE, $page);

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
        $admin = $this->users->admin();
        $editor = $this->users->editor();
        $this->actingAs($admin);
        $page = $this->entities->page();
        $this->activityService->add(ActivityType::PAGE_CREATE, $page);

        $this->actingAs($editor);
        $chapter = $this->entities->chapter();
        $this->activityService->add(ActivityType::CHAPTER_UPDATE, $chapter);

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
        $editor = $this->users->editor();
        $page = $this->entities->page();

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

    public function test_ip_address_is_searchable()
    {
        config()->set('app.proxies', '*');
        $editor = $this->users->editor();
        $page = $this->entities->page();

        $this->actingAs($editor)->put($page->getUrl(), [
            'name' => 'Updated page',
            'html' => '<p>Updated content</p>',
        ], [
            'X-Forwarded-For' => '192.123.45.1',
        ])->assertRedirect($page->refresh()->getUrl());

        $this->actingAs($editor)->put($page->getUrl(), [
            'name' => 'Updated page',
            'html' => '<p>Updated content</p>',
        ], [
            'X-Forwarded-For' => '192.122.45.1',
        ])->assertRedirect($page->refresh()->getUrl());

        $resp = $this->asAdmin()->get('/settings/audit?&ip=192.123');
        $resp->assertSee('192.123.45.1');
        $resp->assertDontSee('192.122.45.1');
    }

    public function test_ip_address_not_logged_in_demo_mode()
    {
        config()->set('app.proxies', '*');
        config()->set('app.env', 'demo');
        $editor = $this->users->editor();
        $page = $this->entities->page();

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

    public function test_ip_address_respects_precision_setting()
    {
        config()->set('app.proxies', '*');
        config()->set('app.ip_address_precision', 2);
        $editor = $this->users->editor();
        $page = $this->entities->page();

        $this->actingAs($editor)->put($page->getUrl(), [
            'name' => 'Updated page',
            'html' => '<p>Updated content</p>',
        ], [
            'X-Forwarded-For' => '192.123.45.1',
        ])->assertRedirect($page->refresh()->getUrl());

        $this->assertDatabaseHas('activities', [
            'type'      => ActivityType::PAGE_UPDATE,
            'ip'        => '192.123.x.x',
            'user_id'   => $editor->id,
            'entity_id' => $page->id,
        ]);
    }
}
