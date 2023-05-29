<?php

namespace Tests\Api;

use BookStack\Entities\Models\Page;
use BookStack\Uploads\Image;
use Tests\TestCase;

class ImageGalleryApiTest extends TestCase
{
    use TestsApi;

    protected string $baseEndpoint = '/api/image-gallery';

    public function test_index_endpoint_returns_expected_image_and_count()
    {
        $this->actingAsApiAdmin();
        $imagePage = $this->entities->page();
        $data = $this->files->uploadGalleryImageToPage($this, $imagePage);
        $image = Image::findOrFail($data['response']->id);

        $resp = $this->getJson($this->baseEndpoint . '?count=1&sort=+id');
        $resp->assertJson(['data' => [
            [
                'id' => $image->id,
                'name' => $image->name,
                'url' => $image->url,
                'path' => $image->path,
                'type' => 'gallery',
                'uploaded_to' => $imagePage->id,
                'created_by' => $this->users->admin()->id,
                'updated_by' => $this->users->admin()->id,
            ],
        ]]);

        $resp->assertJson(['total' => Image::query()->count()]);
    }

    public function test_index_endpoint_doesnt_show_images_for_those_uploaded_to_non_visible_pages()
    {
        $this->actingAsApiEditor();
        $imagePage = $this->entities->page();
        $data = $this->files->uploadGalleryImageToPage($this, $imagePage);
        $image = Image::findOrFail($data['response']->id);

        $resp = $this->getJson($this->baseEndpoint . '?filter[id]=' . $image->id);
        $resp->assertJsonCount(1, 'data');
        $resp->assertJson(['total' => 1]);

        $this->permissions->disableEntityInheritedPermissions($imagePage);

        $resp = $this->getJson($this->baseEndpoint . '?filter[id]=' . $image->id);
        $resp->assertJsonCount(0, 'data');
        $resp->assertJson(['total' => 0]);
    }

    public function test_index_endpoint_doesnt_show_other_image_types()
    {
        $this->actingAsApiEditor();
        $imagePage = $this->entities->page();
        $data = $this->files->uploadGalleryImageToPage($this, $imagePage);
        $image = Image::findOrFail($data['response']->id);

        $typesByCountExpectation = [
            'cover_book' => 0,
            'drawio' => 1,
            'gallery' => 1,
            'user' => 0,
            'system' => 0,
        ];

        foreach ($typesByCountExpectation as $type => $count) {
            $image->type = $type;
            $image->save();

            $resp = $this->getJson($this->baseEndpoint . '?filter[id]=' . $image->id);
            $resp->assertJsonCount($count, 'data');
            $resp->assertJson(['total' => $count]);
        }
    }

    public function test_create_endpoint()
    {
        $this->actingAsApiAdmin();

        $imagePage = $this->entities->page();
        $resp = $this->call('POST', $this->baseEndpoint, [
            'type' => 'gallery',
            'uploaded_to' => $imagePage->id,
            'name' => 'My awesome image!',
        ], [], [
            'image' => $this->files->uploadedImage('my-cool-image.png'),
        ]);

        $resp->assertStatus(200);

        $image = Image::query()->where('uploaded_to', '=', $imagePage->id)->first();
        $expectedUser = [
            'id' => $this->users->admin()->id,
            'name' => $this->users->admin()->name,
            'slug' => $this->users->admin()->slug,
        ];
        $resp->assertJson([
            'id' => $image->id,
            'name' => 'My awesome image!',
            'url' => $image->url,
            'path' => $image->path,
            'type' => 'gallery',
            'uploaded_to' => $imagePage->id,
            'created_by' => $expectedUser,
            'updated_by' => $expectedUser,
        ]);
    }

    public function test_create_endpoint_requires_image_create_permissions()
    {
        $user = $this->users->editor();
        $this->actingAsForApi($user);
        $this->permissions->removeUserRolePermissions($user, ['image-create-all']);

        $makeRequest = function () {
            return $this->call('POST', $this->baseEndpoint, []);
        };

        $resp = $makeRequest();
        $resp->assertStatus(403);

        $this->permissions->grantUserRolePermissions($user, ['image-create-all']);

        $resp = $makeRequest();
        $resp->assertStatus(422);
    }

    public function test_create_fails_if_uploaded_to_not_visible_or_not_exists()
    {
        $this->actingAsApiEditor();

        $makeRequest = function (int $uploadedTo) {
            return $this->call('POST', $this->baseEndpoint, [
                'type' => 'gallery',
                'uploaded_to' => $uploadedTo,
                'name' => 'My awesome image!',
            ], [], [
                'image' => $this->files->uploadedImage('my-cool-image.png'),
            ]);
        };

        $page = $this->entities->page();
        $this->permissions->disableEntityInheritedPermissions($page);
        $resp = $makeRequest($page->id);
        $resp->assertStatus(404);

        $resp = $makeRequest(Page::query()->max('id') + 55);
        $resp->assertStatus(404);
    }

    public function test_create_has_restricted_types()
    {
        $this->actingAsApiEditor();

        $typesByStatusExpectation = [
            'cover_book' => 422,
            'drawio' => 200,
            'gallery' => 200,
            'user' => 422,
            'system' => 422,
        ];

        $makeRequest = function (string $type) {
            return $this->call('POST', $this->baseEndpoint, [
                'type' => $type,
                'uploaded_to' => $this->entities->page()->id,
                'name' => 'My awesome image!',
            ], [], [
                'image' => $this->files->uploadedImage('my-cool-image.png'),
            ]);
        };

        foreach ($typesByStatusExpectation as $type => $status) {
            $resp = $makeRequest($type);
            $resp->assertStatus($status);
        }
    }

    public function test_create_will_use_file_name_if_no_name_provided_in_request()
    {
        $this->actingAsApiEditor();

        $imagePage = $this->entities->page();
        $resp = $this->call('POST', $this->baseEndpoint, [
            'type' => 'gallery',
            'uploaded_to' => $imagePage->id,
        ], [], [
            'image' => $this->files->uploadedImage('my-cool-image.png'),
        ]);
        $resp->assertStatus(200);

        $this->assertDatabaseHas('images', [
            'type' => 'gallery',
            'uploaded_to' => $imagePage->id,
            'name' => 'my-cool-image.png',
        ]);
    }

    public function test_read_endpoint()
    {
        $this->actingAsApiAdmin();
        $imagePage = $this->entities->page();
        $data = $this->files->uploadGalleryImageToPage($this, $imagePage);
        $image = Image::findOrFail($data['response']->id);

        $resp = $this->getJson($this->baseEndpoint . "/{$image->id}");
        $resp->assertStatus(200);

        $expectedUser = [
            'id' => $this->users->admin()->id,
            'name' => $this->users->admin()->name,
            'slug' => $this->users->admin()->slug,
        ];

        $displayUrl = $image->getThumb(1680, null, true);
        $resp->assertJson([
            'id' => $image->id,
            'name' => $image->name,
            'url' => $image->url,
            'path' => $image->path,
            'type' => 'gallery',
            'uploaded_to' => $imagePage->id,
            'created_by' => $expectedUser,
            'updated_by' => $expectedUser,
            'content' => [
                'html' => "<a href=\"{$image->url}\" target=\"_blank\"><img src=\"{$displayUrl}\" alt=\"{$image->name}\"></a>",
                'markdown' => "![{$image->name}]({$displayUrl})",
            ],
        ]);
        $this->assertStringStartsWith('http://', $resp->json('thumbs.gallery'));
        $this->assertStringStartsWith('http://', $resp->json('thumbs.display'));
    }

    public function test_read_endpoint_provides_different_content_for_drawings()
    {
        $this->actingAsApiAdmin();
        $imagePage = $this->entities->page();
        $data = $this->files->uploadGalleryImageToPage($this, $imagePage);
        $image = Image::findOrFail($data['response']->id);

        $image->type = 'drawio';
        $image->save();

        $resp = $this->getJson($this->baseEndpoint . "/{$image->id}");
        $resp->assertStatus(200);

        $drawing = "<div drawio-diagram=\"{$image->id}\"><img src=\"{$image->url}\"></div>";
        $resp->assertJson([
            'id' => $image->id,
            'content' => [
                'html' => $drawing,
                'markdown' => $drawing,
            ],
        ]);
    }

    public function test_read_endpoint_does_not_show_if_no_permissions_for_related_page()
    {
        $this->actingAsApiEditor();
        $imagePage = $this->entities->page();
        $data = $this->files->uploadGalleryImageToPage($this, $imagePage);
        $image = Image::findOrFail($data['response']->id);

        $this->permissions->disableEntityInheritedPermissions($imagePage);

        $resp = $this->getJson($this->baseEndpoint . "/{$image->id}");
        $resp->assertStatus(404);
    }

    public function test_update_endpoint()
    {
        $this->actingAsApiAdmin();
        $imagePage = $this->entities->page();
        $data = $this->files->uploadGalleryImageToPage($this, $imagePage);
        $image = Image::findOrFail($data['response']->id);

        $resp = $this->putJson($this->baseEndpoint . "/{$image->id}", [
            'name' => 'My updated image name!',
        ]);

        $resp->assertStatus(200);
        $resp->assertJson([
            'id' => $image->id,
            'name' => 'My updated image name!',
        ]);
        $this->assertDatabaseHas('images', [
            'id' => $image->id,
            'name' => 'My updated image name!',
        ]);
    }

    public function test_update_existing_image_file()
    {
        $this->actingAsApiAdmin();
        $imagePage = $this->entities->page();
        $data = $this->files->uploadGalleryImageToPage($this, $imagePage);
        $image = Image::findOrFail($data['response']->id);

        $this->assertFileEquals($this->files->testFilePath('test-image.png'), public_path($data['path']));

        $resp = $this->call('PUT', $this->baseEndpoint . "/{$image->id}", [], [], [
            'image' => $this->files->uploadedImage('my-cool-image.png', 'compressed.png'),
        ]);

        $resp->assertStatus(200);
        $this->assertFileEquals($this->files->testFilePath('compressed.png'), public_path($data['path']));
    }

    public function test_update_endpoint_requires_image_update_permission()
    {
        $user = $this->users->editor();
        $this->actingAsForApi($user);
        $imagePage = $this->entities->page();
        $this->permissions->removeUserRolePermissions($user, ['image-update-all', 'image-update-own']);
        $data = $this->files->uploadGalleryImageToPage($this, $imagePage);
        $image = Image::findOrFail($data['response']->id);

        $resp = $this->putJson($this->baseEndpoint . "/{$image->id}", ['name' => 'My new name']);
        $resp->assertStatus(403);
        $resp->assertJson($this->permissionErrorResponse());

        $this->permissions->grantUserRolePermissions($user, ['image-update-all']);
        $resp = $this->putJson($this->baseEndpoint . "/{$image->id}", ['name' => 'My new name']);
        $resp->assertStatus(200);
    }

    public function test_delete_endpoint()
    {
        $this->actingAsApiAdmin();
        $imagePage = $this->entities->page();
        $data = $this->files->uploadGalleryImageToPage($this, $imagePage);
        $image = Image::findOrFail($data['response']->id);
        $this->assertDatabaseHas('images', ['id' => $image->id]);

        $resp = $this->deleteJson($this->baseEndpoint . "/{$image->id}");

        $resp->assertStatus(204);
        $this->assertDatabaseMissing('images', ['id' => $image->id]);
    }

    public function test_delete_endpoint_requires_image_delete_permission()
    {
        $user = $this->users->editor();
        $this->actingAsForApi($user);
        $imagePage = $this->entities->page();
        $this->permissions->removeUserRolePermissions($user, ['image-delete-all', 'image-delete-own']);
        $data = $this->files->uploadGalleryImageToPage($this, $imagePage);
        $image = Image::findOrFail($data['response']->id);

        $resp = $this->deleteJson($this->baseEndpoint . "/{$image->id}");
        $resp->assertStatus(403);
        $resp->assertJson($this->permissionErrorResponse());

        $this->permissions->grantUserRolePermissions($user, ['image-delete-all']);
        $resp = $this->deleteJson($this->baseEndpoint . "/{$image->id}");
        $resp->assertStatus(204);
    }
}
