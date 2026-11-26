<?php

namespace Tests\Api;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\PageRevision;
use BookStack\Permissions\Permission;
use Tests\TestCase;

class PageRevisionsApiTest extends TestCase
{
    use TestsApi;

    protected string $baseEndpoint = '/api/revisions';

    protected function createRevisions(Page $page, int $count, $data = []): void
    {
        for ($i = 0; $i < $count; $i++) {
            $updateData = empty($data) ? [
                'name' => "Updated revision {$i}",
                'html' => "<p>Revision content {$i}</p>",
            ] : $data;
            $this->entities->updatePage($page, $updateData);
        }

        $page->refresh();
    }

    public function test_index_endpoint_returns_revisions()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();
        $this->createRevisions($page, 2);

        $resp = $this->getJson("{$this->baseEndpoint}?sort=-id");
        $resp->assertOk();

        $latest = PageRevision::query()->where('page_id', '=', $page->id)->orderByDesc('id')->first();
        $resp->assertJson([
            'data' => [
                [
                    'id'              => $latest->id,
                    'page_id'         => $page->id,
                    'name'            => $latest->name,
                    'slug'            => $latest->slug,
                    'changelog'       => $latest->changelog,
                    'revision_number' => $latest->revision_number,
                    'created_by'      => $latest->created_by,
                ],
            ],
        ]);
        $resp->assertJson(['total' => PageRevision::query()->count()]);
    }

    public function test_index_content_does_not_include_content()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();
        $this->createRevisions($page, 1);

        $resp = $this->getJson("{$this->baseEndpoint}");
        $resp->assertOk();
        $resp->assertJsonMissingPath('data.0.html');
        $resp->assertJsonMissingPath('data.0.markdown');
        $resp->assertJsonMissingPath('data.0.text');
    }

    public function test_index_endpoint_filters_based_on_visibility()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();
        $this->createRevisions($page, 1);

        $revision = PageRevision::query()->where('page_id', '=', $page->id)->orderByDesc('id')->first();
        $expected = [
            'data' => [
                ['id' => $revision->id]
            ],
        ];

        $resp = $this->getJson($this->baseEndpoint);
        $resp->assertOk();
        $resp->assertJson($expected);

        $this->permissions->disableEntityInheritedPermissions($page);

        $resp = $this->getJson($this->baseEndpoint);
        $resp->assertOk();
        $resp->assertJsonMissing($expected);
    }

    public function test_endpoints_require_revision_view_all_permission()
    {
        $editor = $this->users->editor();
        $this->actingAsForApi($editor);
        $page = $this->entities->page();
        $this->createRevisions($page, 1);
        $revision = PageRevision::query()->where('page_id', '=', $page->id)->orderByDesc('id')->first();

        $this->permissions->removeUserRolePermissions($editor, [Permission::RevisionViewAll]);

        $endpoints = [
            $this->baseEndpoint,
            "{$this->baseEndpoint}/{$revision->id}",
            "{$this->baseEndpoint}/{$revision->id}/changes",
        ];

        foreach ($endpoints as $endpoint) {
            $resp = $this->getJson($endpoint);
            $resp->assertStatus(403);
            $resp->assertJson($this->permissionErrorResponse());
        }

        $this->permissions->grantUserRolePermissions($editor, [Permission::RevisionViewAll]);

        foreach ($endpoints as $endpoint) {
            $resp = $this->getJson($endpoint);
            $resp->assertOk();
        }
    }

    public function test_read_endpoint_returns_revision_details_and_content()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();

        $this->createRevisions($page, 1, [
            'name' => 'Older revision name',
            'html' => '<p>Older revision content</p>',
        ]);
        $revision = PageRevision::query()->where('page_id', '=', $page->id)->orderByDesc('id')->first();

        $resp = $this->getJson("{$this->baseEndpoint}/{$revision->id}");
        $resp->assertOk();
        $resp->assertJson([
            'id'              => $revision->id,
            'page_id'         => $page->id,
            'name'            => 'Older revision name',
            'revision_number' => $revision->revision_number,
            'created_by'      => ['id' => $revision->created_by],
            'raw_html'        => $revision->html,
        ]);
        $resp->assertJsonStructure([
            'id', 'page_id', 'name', 'slug', 'book_slug', 'changelog',
            'revision_number', 'created_at', 'created_by',
            'html', 'raw_html', 'markdown',
        ]);

        $this->assertStringContainsString('Older revision content', $resp->json('html'));
    }

    public function test_read_endpoint_returns_markdown_when_used()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();
        $this->createRevisions($page, 1, [
            'name'     => 'Markdown revision',
            'markdown' => '# A markdown heading',
        ]);
        $revision = PageRevision::query()->where('page_id', '=', $page->id)->orderByDesc('id')->first();

        $resp = $this->getJson("{$this->baseEndpoint}/{$revision->id}");
        $resp->assertOk();
        $resp->assertJsonPath('markdown', '# A markdown heading');
    }

    public function test_read_endpoint_provides_raw_html()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();
        $html = "<p>testing</p><script>alert('danger')</script><h1>Hello</h1>";
        $this->createRevisions($page, 1, [
            'name' => 'Script revision',
            'html' => $html,
        ]);
        $revision = PageRevision::query()->where('page_id', '=', $page->id)->orderByDesc('id')->first();

        $resp = $this->getJson("{$this->baseEndpoint}/{$revision->id}");
        $resp->assertOk();
        $this->assertEquals($revision->html, $resp->json('raw_html'));
        $this->assertNotEquals($resp->json('raw_html'), $resp->json('html'));
    }

    public function test_read_endpoint_returns_not_found()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();
        $this->createRevisions($page, 1);
        $id = PageRevision::query()->orderByDesc('id')->first()->id + 1;

        $this->getJson("{$this->baseEndpoint}/{$id}")->assertNotFound();
    }

    public function test_read_endpoint_returns_not_found_for_non_visible_page()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();
        $this->createRevisions($page, 1);
        $revision = PageRevision::query()->where('page_id', '=', $page->id)->orderByDesc('id')->first();

        $this->permissions->disableEntityInheritedPermissions($page);

        $this->getJson("{$this->baseEndpoint}/{$revision->id}")->assertNotFound();
    }

    public function test_changes_endpoint()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();
        $this->createRevisions($page, 1, [
            'name' => 'Changes',
            'html' => '<p>A cool cat</p>',
        ]);
        $priorRevision = PageRevision::query()->where('page_id', '=', $page->id)->orderByDesc('id')->first();
        $this->createRevisions($page, 1, [
            'name' => 'Changes',
            'html' => '<p>A cool dog</p>',
        ]);
        $revision = PageRevision::query()->where('page_id', '=', $page->id)->orderByDesc('id')->first();

        $resp = $this->getJson("{$this->baseEndpoint}/{$revision->id}/changes");
        $resp->assertOk();
        $resp->assertJson([
            'html_diff' => '<p id="bkmrk-a-cool-dog">A cool <del class="diffmod">cat</del><ins class="diffmod">dog</ins></p>',
            'previous_revision_id' => $priorRevision->id,
        ]);
    }

    public function test_changes_endpoint_when_no_prior_revision()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();
        $this->createRevisions($page, 1, [
            'name' => 'Changes',
            'html' => '<p>A cool cat</p>',
        ]);
        $revision = PageRevision::query()->where('page_id', '=', $page->id)->orderByDesc('id')->first();

        $resp = $this->getJson("{$this->baseEndpoint}/{$revision->id}/changes");
        $resp->assertOk();
        $resp->assertJson([
            'html_diff' => '<p id="bkmrk-a-cool-cat"><ins class="diffins">A cool cat</ins></p>',
            'previous_revision_id' => null,
        ]);
    }

    public function test_changes_endpoint_returns_not_found_for_non_visible_page()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();
        $this->createRevisions($page, 1, [
            'name' => 'Changes',
            'html' => '<p>A cool cat</p>',
        ]);
        $revision = PageRevision::query()->where('page_id', '=', $page->id)->orderByDesc('id')->first();

        $this->permissions->disableEntityInheritedPermissions($page);

        $resp = $this->getJson("{$this->baseEndpoint}/{$revision->id}/changes");
        $resp->assertNotFound();
    }
}
