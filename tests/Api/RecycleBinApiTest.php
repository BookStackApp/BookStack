<?php

namespace Tests\Api;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Deletion;
use BookStack\Entities\Models\Page;
use Illuminate\Support\Collection;
use Tests\TestCase;

class RecycleBinApiTest extends TestCase
{
    use TestsApi;

    protected string $baseEndpoint = '/api/recycle_bin';

    protected array $endpointMap = [
        ['get', '/api/recycle_bin'],
        ['put', '/api/recycle_bin/1'],
        ['delete', '/api/recycle_bin/1'],
    ];

    public function test_settings_manage_permission_needed_for_all_endpoints()
    {
        $editor = $this->getEditor();
        $this->giveUserPermissions($editor, ['settings-manage']);
        $this->actingAs($editor);

        foreach ($this->endpointMap as [$method, $uri]) {
            $resp = $this->json($method, $uri);
            $resp->assertStatus(403);
            $resp->assertJson($this->permissionErrorResponse());
        }
    }

    public function test_restrictions_manage_all_permission_neeed_for_all_endpoints()
    {
        $editor = $this->getEditor();
        $this->giveUserPermissions($editor, ['restrictions-manage-all']);
        $this->actingAs($editor);
        
        foreach ($this->endpointMap as [$method, $uri]) {
            $resp = $this->json($method, $uri);
            $resp->assertStatus(403);
            $resp->assertJson($this->permissionErrorResponse());
        }
    }

    public function test_index_endpoint_returns_expected_page()
    {
        $this->actingAsAuthorizedUser();

        $page = Page::query()->first();
        $book = Book::query()->whereHas('pages')->whereHas('chapters')->withCount(['pages', 'chapters'])->first();
        $editor = $this->getEditor();
        $this->actingAs($editor)->delete($page->getUrl());
        $this->actingAs($editor)->delete($book->getUrl());

        $deletions = Deletion::query()->orderBy('id')->get();

        $resp = $this->getJson($this->baseEndpoint);

        $expectedData = $deletions
            ->zip([$page, $book])
            ->map(function (Collection $data) use ($editor) {
                return [
                    'id'                => $data[0]->id,
                    'deleted_by'        => $editor->getKey(),
                    'created_at'        => $data[0]->created_at->toJson(),
                    'updated_at'        => $data[0]->updated_at->toJson(),
                    'deletable_type'    => $data[1]->getMorphClass(),
                    'deletable_id'      => $data[1]->getKey(),
                ];
            });

        $resp->assertJson([
            'data' => $expectedData->values()->all(), 
            'total' => 2,
        ]);
    }

    public function test_index_endpoint_returns_children()
    {
        $this->actingAsAuthorizedUser();
        
        $book = Book::query()->whereHas('pages')->whereHas('chapters')->withCount(['pages', 'chapters'])->first();
        $editor = $this->getEditor();
        $this->actingAs($editor)->delete($book->getUrl());

        $deletion = Deletion::query()->orderBy('id')->first();

        $resp = $this->getJson($this->baseEndpoint);

        $expectedData = [
            [
                'id'                => $deletion->getKey(),
                'deleted_by'        => $editor->getKey(),
                'created_at'        => $deletion->created_at->toJson(),
                'updated_at'        => $deletion->updated_at->toJson(),
                'deletable_type'    => $book->getMorphClass(),
                'deletable_id'      => $book->getKey(),
                'children'          => [
                    'Bookstack\Page'    => $book->pages_count,
                    'Bookstack\Chapter' => $book->chapters_count,
                ],
                'parent' => null,
            ]
        ];

        $resp->assertJson([
            'data' => $expectedData, 
            'total' => 1,
        ]);
    }

    public function test_index_endpoint_returns_parent()
    {
        $this->actingAsAuthorizedUser();

        $page = Page::query()->whereHas('chapter')->with('chapter')->first();

        $editor = $this->getEditor();
        $this->actingAs($editor)->delete($page->getUrl());

        $deletion = Deletion::query()->orderBy('id')->first();

        $resp = $this->getJson($this->baseEndpoint);

        $expectedData = [
            [
                'id'                => $deletion->getKey(),
                'deleted_by'        => $editor->getKey(),
                'created_at'        => $deletion->created_at->toJson(),
                'updated_at'        => $deletion->updated_at->toJson(),
                'deletable_type'    => $page->getMorphClass(),
                'deletable_id'      => $page->getKey(),
                'parent'            => [
                    'type'  => 'BookStack\Chapter',
                    'id'    => $page->chapter->getKey()
                ],
                'children' => null,
            ]
        ];

        $resp->assertJson([
            'data' => $expectedData, 
            'total' => 1
        ]);
    }

    public function test_restore_endpoint()
    {
        $this->actingAsAuthorizedUser();
        
        $page = Page::query()->first();
        $editor = $this->getEditor();
        $this->actingAs($editor)->delete($page->getUrl());
        $page->refresh();

        $deletion = Deletion::query()->orderBy('id')->first();

        $this->assertDatabaseHas('pages', [
            'id' => $page->getKey(),
            'deleted_at' => $page->deleted_at,
        ]);

        $this->putJson($this->baseEndpoint . '/' . $deletion->getKey());

        $this->assertDatabaseHas('pages', [
            'id' => $page->getKey(),
            'deleted_at' => null,
        ]);
    }

    public function test_destroy_endpoint()
    {
        $this->actingAsAuthorizedUser();
        
        $page = Page::query()->first();
        $editor = $this->getEditor();
        $this->actingAs($editor)->delete($page->getUrl());
        $page->refresh();

        $deletion = Deletion::query()->orderBy('id')->first();

        $this->assertDatabaseHas('pages', [
            'id' => $page->getKey(),
            'deleted_at' => $page->deleted_at,
        ]);

        $this->deleteJson($this->baseEndpoint . '/' . $deletion->getKey());
        $this->assertDatabaseMissing('pages', ['id' => $page->getKey()]);
    }

    private function actingAsAuthorizedUser()
    {
        $editor = $this->getEditor();
        $this->giveUserPermissions($editor, ['restrictions-manage-all']);
        $this->giveUserPermissions($editor, ['settings-manage']);
        $this->actingAs($editor);
    }
}
