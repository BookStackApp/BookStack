<?php

namespace Tests\Api;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\PageRevision;
use BookStack\Permissions\Permission;
use Tests\TestCase;

class PageRevisionsApiTest extends TestCase
{
    use TestsApi;

    protected string $baseEndpoint = '/api/pages';
    protected string $revisionEndpoint = '/api/revisions';

    protected function createRevisions(Page $page, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $this->putJson("{$this->baseEndpoint}/{$page->id}", [
                'name' => "Updated revision {$i}",
                'html' => "<p>Revision content {$i}</p>",
            ])->assertOk();
        }

        $page->refresh();
    }

    public function test_index_endpoint_returns_page_revisions()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();
        $this->createRevisions($page, 2);

        $resp = $this->getJson("{$this->baseEndpoint}/{$page->id}/revisions?sort=-id");
        $resp->assertOk();

        $latest = PageRevision::query()->where('page_id', '=', $page->id)->orderByDesc('id')->first();
        $resp->assertJson([
            'data' => [
                [
                    'id'              => $latest->id,
                    'page_id'         => $page->id,
                    'name'            => $latest->name,
                    'slug'            => $latest->slug,
                    'type'            => 'version',
                    'summary'         => $latest->summary,
                    'revision_number' => $latest->revision_number,
                    'created_by'      => $latest->created_by,
                ],
            ],
            'total' => PageRevision::query()
                ->where('page_id', '=', $page->id)
                ->where('type', '=', 'version')
                ->count(),
        ]);
    }

    public function test_endpoints_require_revision_view_all_permission()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();
        $this->createRevisions($page, 1);
        $revision = PageRevision::query()->where('page_id', '=', $page->id)->orderByDesc('id')->first();

        $this->permissions->removeUserRolePermissions($this->users->editor(), [Permission::RevisionViewAll]);

        $endpoints = [
            "{$this->baseEndpoint}/{$page->id}/revisions",
            "{$this->revisionEndpoint}/{$revision->id}",
        ];

        foreach ($endpoints as $endpoint) {
            $resp = $this->getJson($endpoint);
            $resp->assertStatus(403);
            $resp->assertJson($this->permissionErrorResponse());
        }
    }

    public function test_index_endpoint_returns_not_found_for_non_visible_page()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();
        $this->permissions->disableEntityInheritedPermissions($page);

        $this->getJson("{$this->baseEndpoint}/{$page->id}/revisions")->assertNotFound();
    }

    public function test_read_endpoint_returns_revision_details_and_content()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();

        $this->putJson("{$this->baseEndpoint}/{$page->id}", [
            'name' => 'Older revision name',
            'html' => '<p>Older revision content</p>',
        ])->assertOk();
        $revision = PageRevision::query()->where('page_id', '=', $page->id)->orderByDesc('id')->first();

        $this->putJson("{$this->baseEndpoint}/{$page->id}", [
            'name' => 'Newer revision name',
            'html' => '<p>Newer revision content</p>',
        ])->assertOk();

        $resp = $this->getJson("{$this->revisionEndpoint}/{$revision->id}");
        $resp->assertOk();
        $resp->assertJson([
            'id'              => $revision->id,
            'page_id'         => $page->id,
            'name'            => 'Older revision name',
            'type'            => 'version',
            'revision_number' => $revision->revision_number,
            'created_by'      => ['id' => $revision->created_by],
            'raw_html'        => $revision->html,
        ]);
        $resp->assertJsonStructure([
            'id', 'page_id', 'name', 'slug', 'book_slug', 'type', 'summary',
            'revision_number', 'created_at', 'updated_at', 'created_by',
            'html', 'raw_html', 'markdown',
        ]);

        $this->assertStringContainsString('Older revision content', $resp->json('html'));
        $this->assertStringNotContainsString('Newer revision content', $resp->json('html'));
    }

    public function test_read_endpoint_returns_markdown_when_used()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();
        $this->putJson("{$this->baseEndpoint}/{$page->id}", [
            'name'     => 'Markdown revision',
            'markdown' => '# A markdown heading',
        ])->assertOk();

        $revision = PageRevision::query()->where('page_id', '=', $page->id)->orderByDesc('id')->first();
        $resp = $this->getJson("{$this->revisionEndpoint}/{$revision->id}");
        $resp->assertOk();
        $resp->assertJsonPath('markdown', '# A markdown heading');
    }

    public function test_read_endpoint_provides_raw_html()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();
        $html = "<p>testing</p><script>alert('danger')</script><h1>Hello</h1>";
        $this->putJson("{$this->baseEndpoint}/{$page->id}", [
            'name' => 'Script revision',
            'html' => $html,
        ])->assertOk();

        $revision = PageRevision::query()->where('page_id', '=', $page->id)->orderByDesc('id')->first();
        $resp = $this->getJson("{$this->revisionEndpoint}/{$revision->id}");
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

        $this->getJson("{$this->revisionEndpoint}/{$id}")->assertNotFound();
    }

    public function test_read_endpoint_returns_not_found_for_non_visible_page()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();
        $this->createRevisions($page, 1);
        $revision = PageRevision::query()->where('page_id', '=', $page->id)->orderByDesc('id')->first();

        $this->permissions->disableEntityInheritedPermissions($page);

        $this->getJson("{$this->revisionEndpoint}/{$revision->id}")->assertNotFound();
    }

    public function test_revisions_endpoints_do_not_expose_drafts()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();
        $this->createRevisions($page, 1);
        $versionCount = PageRevision::query()
            ->where('page_id', '=', $page->id)
            ->where('type', '=', 'version')
            ->count();

        $draft = PageRevision::factory()->create([
            'page_id' => $page->id,
            'type'    => 'update_draft',
        ]);

        $listResp = $this->getJson("{$this->baseEndpoint}/{$page->id}/revisions?count=1000");
        $listResp->assertOk();
        $listResp->assertJsonPath('total', $versionCount);
        $this->assertNotContains($draft->id, array_column($listResp->json('data'), 'id'));

        $this->getJson("{$this->revisionEndpoint}/{$draft->id}")->assertNotFound();
    }

    public function test_listing_does_not_include_content()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();
        $this->createRevisions($page, 1);

        $resp = $this->getJson("{$this->baseEndpoint}/{$page->id}/revisions");
        $resp->assertOk();
        $resp->assertJsonMissingPath('data.0.html');
        $resp->assertJsonMissingPath('data.0.markdown');
        $resp->assertJsonMissingPath('data.0.text');
    }
}
