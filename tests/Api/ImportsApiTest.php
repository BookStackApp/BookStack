<?php

namespace Tests\Api;

use BookStack\Entities\Models\Page;
use BookStack\Exports\Import;
use Tests\Exports\ZipTestHelper;
use Tests\TestCase;

class ImportsApiTest extends TestCase
{
    use TestsApi;

    protected string $baseEndpoint = '/api/imports';

    public function test_create_and_run(): void
    {
        $book = $this->entities->book();
        $zip = ZipTestHelper::zipUploadFromData([
            'page' => [
                'name' => 'My API import page',
                'tags' => [
                    [
                        'name' => 'My api tag',
                        'value' => 'api test value'
                    ]
                ],
            ],
        ]);

        $resp = $this->actingAsApiAdmin()->call('POST', $this->baseEndpoint, [], [], ['file' => $zip]);
        $resp->assertStatus(200);

        $importId = $resp->json('id');
        $import = Import::query()->findOrFail($importId);
        $this->assertEquals('page', $import->type);

        $resp = $this->post($this->baseEndpoint . "/{$import->id}", [
            'parent_type' => 'book',
            'parent_id' => $book->id,
        ]);
        $resp->assertJson([
            'name' => 'My API import page',
            'book_id' => $book->id,
        ]);
        $resp->assertJsonMissingPath('book');

        $page = Page::query()->where('name', '=', 'My API import page')->first();
        $this->assertEquals('My api tag', $page->tags()->first()->name);
    }

    public function test_create_validation_error(): void
    {
        $zip = ZipTestHelper::zipUploadFromData([
            'page' => [
                'tags' => [
                    [
                        'name' => 'My api tag',
                        'value' => 'api test value'
                    ]
                ],
            ],
        ]);

        $resp = $this->actingAsApiAdmin()->call('POST', $this->baseEndpoint, [], [], ['file' => $zip]);
        $resp->assertStatus(422);
        $message = $resp->json('message');

        $this->assertStringContainsString('ZIP upload failed with the following validation errors:', $message);
        $this->assertStringContainsString('[page.name] The name field is required.', $message);
    }

    public function test_list(): void
    {
        $imports = Import::factory()->count(10)->create();

        $resp = $this->actingAsApiAdmin()->get($this->baseEndpoint);
        $resp->assertJsonCount(10, 'data');
        $resp->assertJsonPath('total', 10);

        $firstImport = $imports->first();
        $resp = $this->actingAsApiAdmin()->get($this->baseEndpoint . '?filter[id]=' . $firstImport->id);
        $resp->assertJsonCount(1, 'data');
        $resp->assertJsonPath('data.0.id', $firstImport->id);
        $resp->assertJsonPath('data.0.name', $firstImport->name);
        $resp->assertJsonPath('data.0.size', $firstImport->size);
        $resp->assertJsonPath('data.0.type', $firstImport->type);
    }

    public function test_list_visibility_limited(): void
    {
        $user = $this->users->editor();
        $admin = $this->users->admin();
        $userImport = Import::factory()->create(['name' => 'MySuperUserImport', 'created_by' => $user->id]);
        $adminImport = Import::factory()->create(['name' => 'MySuperAdminImport', 'created_by' => $admin->id]);
        $this->permissions->grantUserRolePermissions($user, ['content-import']);

        $resp = $this->actingAsForApi($user)->get($this->baseEndpoint);
        $resp->assertJsonCount(1, 'data');
        $resp->assertJsonPath('data.0.name', 'MySuperUserImport');

        $this->permissions->grantUserRolePermissions($user, ['settings-manage']);

        $resp = $this->actingAsForApi($user)->get($this->baseEndpoint);
        $resp->assertJsonCount(2, 'data');
        $resp->assertJsonPath('data.1.name', 'MySuperAdminImport');
    }

    public function test_read(): void
    {
        $zip = ZipTestHelper::zipUploadFromData([
            'book' => [
                'name' => 'My API import book',
                'pages' => [
                    [
                        'name' => 'My import page',
                        'tags' => [
                            [
                                'name' => 'My api tag',
                                'value' => 'api test value'
                            ]
                        ]
                    ]
                ],
            ],
        ]);

        $resp = $this->actingAsApiAdmin()->call('POST', $this->baseEndpoint, [], [], ['file' => $zip]);
        $resp->assertStatus(200);

        $resp = $this->get($this->baseEndpoint . "/{$resp->json('id')}");
        $resp->assertStatus(200);

        $resp->assertJsonPath('details.name', 'My API import book');
        $resp->assertJsonPath('details.pages.0.name', 'My import page');
        $resp->assertJsonPath('details.pages.0.tags.0.name', 'My api tag');
        $resp->assertJsonMissingPath('metadata');
    }

    public function test_delete(): void
    {
        $import = Import::factory()->create();

        $resp = $this->actingAsApiAdmin()->delete($this->baseEndpoint . "/{$import->id}");
        $resp->assertStatus(204);
    }

    public function test_content_import_permissions_needed(): void
    {
        $user = $this->users->viewer();
        $this->permissions->grantUserRolePermissions($user, ['access-api']);
        $this->actingAsForApi($user);
        $requests = [
             ['GET', $this->baseEndpoint],
             ['POST', $this->baseEndpoint],
             ['GET', $this->baseEndpoint . "/1"],
             ['POST', $this->baseEndpoint . "/1"],
             ['DELETE', $this->baseEndpoint . "/1"],
        ];

        foreach ($requests as $request) {
            [$method, $endpoint] = $request;
            $resp = $this->json($method, $endpoint);
            $resp->assertStatus(403);
        }

        $this->permissions->grantUserRolePermissions($user, ['content-import']);

        foreach ($requests as $request) {
            [$method, $endpoint] = $request;
            $resp = $this->call($method, $endpoint);
            $this->assertNotEquals(403, $resp->status(), "A {$method} request to {$endpoint} returned 403");
        }
    }
}
