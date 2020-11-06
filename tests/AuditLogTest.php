<?php namespace Tests;

use BookStack\Actions\Activity;
use BookStack\Actions\ActivityService;
use BookStack\Auth\UserRepo;
use BookStack\Entities\Managers\TrashCan;
use BookStack\Entities\Page;
use BookStack\Entities\Repos\PageRepo;
use Carbon\Carbon;

class AuditLogTest extends TestCase
{

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
        app(ActivityService::class)->add($page, 'page_create', $page->book->id);
        $activity = Activity::query()->orderBy('id', 'desc')->first();

        $resp = $this->get('settings/audit');
        $resp->assertSeeText($page->name);
        $resp->assertSeeText('page_create');
        $resp->assertSeeText($activity->created_at->toDateTimeString());
        $resp->assertElementContains('.table-user-item', $admin->name);
    }

    public function test_shows_name_for_deleted_items()
    {
        $this->actingAs( $this->getAdmin());
        $page = Page::query()->first();
        $pageName = $page->name;
        app(ActivityService::class)->add($page, 'page_create', $page->book->id);

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
        app(ActivityService::class)->add($page, 'page_create', $page->book->id);

        $this->actingAs($this->getAdmin());
        app(UserRepo::class)->destroy($viewer);

        $resp = $this->get('settings/audit');
        $resp->assertSeeText("[ID: {$viewer->id}] Deleted User");
    }

    public function test_filters_by_key()
    {
        $this->actingAs($this->getAdmin());
        $page = Page::query()->first();
        app(ActivityService::class)->add($page, 'page_create', $page->book->id);

        $resp = $this->get('settings/audit');
        $resp->assertSeeText($page->name);

        $resp = $this->get('settings/audit?event=page_delete');
        $resp->assertDontSeeText($page->name);
    }

    public function test_date_filters()
    {
        $this->actingAs($this->getAdmin());
        $page = Page::query()->first();
        app(ActivityService::class)->add($page, 'page_create', $page->book->id);

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

}