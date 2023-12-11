<?php

namespace Tests\Api;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Deletion;
use Illuminate\Support\Collection;
use Tests\TestCase;

class RecycleBinApiTest extends TestCase
{
    use TestsApi;

    protected string $baseEndpoint = '/api/recycle-bin';

    protected array $endpointMap = [
        ['get', '/api/recycle-bin'],
        ['put', '/api/recycle-bin/1'],
        ['delete', '/api/recycle-bin/1'],
    ];

    public function test_settings_manage_permission_needed_for_all_endpoints()
    {
        $editor = $this->users->editor();
        $this->permissions->grantUserRolePermissions($editor, ['settings-manage']);
        $this->actingAs($editor);

        foreach ($this->endpointMap as [$method, $uri]) {
            $resp = $this->json($method, $uri);
            $resp->assertStatus(403);
            $resp->assertJson($this->permissionErrorResponse());
        }
    }

    public function test_restrictions_manage_all_permission_needed_for_all_endpoints()
    {
        $editor = $this->users->editor();
        $this->permissions->grantUserRolePermissions($editor, ['restrictions-manage-all']);
        $this->actingAs($editor);

        foreach ($this->endpointMap as [$method, $uri]) {
            $resp = $this->json($method, $uri);
            $resp->assertStatus(403);
            $resp->assertJson($this->permissionErrorResponse());
        }
    }

    public function test_index_endpoint_returns_expected_page()
    {
        $admin = $this->users->admin();

        $page = $this->entities->page();
        $book = $this->entities->book();
        $this->actingAs($admin)->delete($page->getUrl());
        $this->delete($book->getUrl());

        $deletions = Deletion::query()->orderBy('id')->get();

        $resp = $this->getJson($this->baseEndpoint);

        $expectedData = $deletions
            ->zip([$page, $book])
            ->map(function (Collection $data) use ($admin) {
                return [
                    'id'                => $data[0]->id,
                    'deleted_by'        => $admin->id,
                    'created_at'        => $data[0]->created_at->toJson(),
                    'updated_at'        => $data[0]->updated_at->toJson(),
                    'deletable_type'    => $data[1]->getMorphClass(),
                    'deletable_id'      => $data[1]->id,
                    'deletable'         => [
                        'name' => $data[1]->name,
                    ],
                ];
            });

        $resp->assertJson([
            'data'  => $expectedData->values()->all(),
            'total' => 2,
        ]);
    }

    public function test_index_endpoint_returns_children_count()
    {
        $admin = $this->users->admin();

        $book = Book::query()->whereHas('pages')->whereHas('chapters')->withCount(['pages', 'chapters'])->first();
        $this->actingAs($admin)->delete($book->getUrl());

        $deletion = Deletion::query()->orderBy('id')->first();

        $resp = $this->getJson($this->baseEndpoint);

        $expectedData = [
            [
                'id'             => $deletion->id,
                'deletable'      => [
                    'pages_count'    => $book->pages_count,
                    'chapters_count' => $book->chapters_count,
                ],
            ],
        ];

        $resp->assertJson([
            'data'  => $expectedData,
            'total' => 1,
        ]);
    }

    public function test_index_endpoint_returns_parent()
    {
        $admin = $this->users->admin();
        $page = $this->entities->pageWithinChapter();

        $this->actingAs($admin)->delete($page->getUrl());
        $deletion = Deletion::query()->orderBy('id')->first();

        $resp = $this->getJson($this->baseEndpoint);

        $expectedData = [
            [
                'id'             => $deletion->id,
                'deletable'      => [
                    'parent' => [
                        'id'   => $page->chapter->id,
                        'name' => $page->chapter->name,
                        'type' => 'chapter',
                    ],
                ],
            ],
        ];

        $resp->assertJson([
            'data'  => $expectedData,
            'total' => 1,
        ]);
    }

    public function test_restore_endpoint()
    {
        $page = $this->entities->page();
        $this->asAdmin()->delete($page->getUrl());
        $page->refresh();

        $deletion = Deletion::query()->orderBy('id')->first();

        $this->assertDatabaseHas('pages', [
            'id'            => $page->id,
            'deleted_at'    => $page->deleted_at,
        ]);

        $resp = $this->putJson($this->baseEndpoint . '/' . $deletion->id);
        $resp->assertJson([
            'restore_count' => 1,
        ]);

        $this->assertDatabaseHas('pages', [
            'id'            => $page->id,
            'deleted_at'    => null,
        ]);
    }

    public function test_destroy_endpoint()
    {
        $page = $this->entities->page();
        $this->asAdmin()->delete($page->getUrl());
        $page->refresh();

        $deletion = Deletion::query()->orderBy('id')->first();

        $this->assertDatabaseHas('pages', [
            'id'            => $page->id,
            'deleted_at'    => $page->deleted_at,
        ]);

        $resp = $this->deleteJson($this->baseEndpoint . '/' . $deletion->id);
        $resp->assertJson([
            'delete_count' => 1,
        ]);

        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }
}
