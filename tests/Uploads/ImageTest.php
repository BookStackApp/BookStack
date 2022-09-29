<?php

namespace Tests\Uploads;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Uploads\Image;
use BookStack\Uploads\ImageService;
use Illuminate\Support\Str;
use Tests\TestCase;

class ImageTest extends TestCase
{
    use UsesImages;

    public function test_image_upload()
    {
        $page = Page::query()->first();
        $admin = $this->getAdmin();
        $this->actingAs($admin);

        $imgDetails = $this->uploadGalleryImage($page);
        $relPath = $imgDetails['path'];

        $this->assertTrue(file_exists(public_path($relPath)), 'Uploaded image found at path: ' . public_path($relPath));

        $this->deleteImage($relPath);

        $this->assertDatabaseHas('images', [
            'url'         => $this->baseUrl . $relPath,
            'type'        => 'gallery',
            'uploaded_to' => $page->id,
            'path'        => $relPath,
            'created_by'  => $admin->id,
            'updated_by'  => $admin->id,
            'name'        => $imgDetails['name'],
        ]);
    }

    public function test_image_display_thumbnail_generation_does_not_increase_image_size()
    {
        $page = Page::query()->first();
        $admin = $this->getAdmin();
        $this->actingAs($admin);

        $originalFile = $this->getTestImageFilePath('compressed.png');
        $originalFileSize = filesize($originalFile);
        $imgDetails = $this->uploadGalleryImage($page, 'compressed.png');
        $relPath = $imgDetails['path'];

        $this->assertTrue(file_exists(public_path($relPath)), 'Uploaded image found at path: ' . public_path($relPath));
        $displayImage = $imgDetails['response']->thumbs->display;

        $displayImageRelPath = implode('/', array_slice(explode('/', $displayImage), 3));
        $displayImagePath = public_path($displayImageRelPath);
        $displayFileSize = filesize($displayImagePath);

        $this->deleteImage($relPath);
        $this->deleteImage($displayImageRelPath);

        $this->assertEquals($originalFileSize, $displayFileSize, 'Display thumbnail generation should not increase image size');
    }

    public function test_image_display_thumbnail_generation_for_apng_images_uses_original_file()
    {
        $page = Page::query()->first();
        $admin = $this->getAdmin();
        $this->actingAs($admin);

        $imgDetails = $this->uploadGalleryImage($page, 'animated.png');
        $this->deleteImage($imgDetails['path']);

        $this->assertStringContainsString('thumbs-', $imgDetails['response']->thumbs->gallery);
        $this->assertStringNotContainsString('thumbs-', $imgDetails['response']->thumbs->display);
    }

    public function test_image_edit()
    {
        $editor = $this->getEditor();
        $this->actingAs($editor);

        $imgDetails = $this->uploadGalleryImage();
        $image = Image::query()->first();

        $newName = Str::random();
        $update = $this->put('/images/' . $image->id, ['name' => $newName]);
        $update->assertSuccessful();
        $update->assertSee($newName);

        $this->deleteImage($imgDetails['path']);

        $this->assertDatabaseHas('images', [
            'type' => 'gallery',
            'name' => $newName,
        ]);
    }

    public function test_gallery_get_list_format()
    {
        $this->asEditor();

        $imgDetails = $this->uploadGalleryImage();
        $image = Image::query()->first();

        $pageId = $imgDetails['page']->id;
        $firstPageRequest = $this->get("/images/gallery?page=1&uploaded_to={$pageId}");
        $firstPageRequest->assertSuccessful();
        $this->withHtml($firstPageRequest)->assertElementExists('div');
        $firstPageRequest->assertSuccessful()->assertSeeText($image->name);

        $secondPageRequest = $this->get("/images/gallery?page=2&uploaded_to={$pageId}");
        $secondPageRequest->assertSuccessful();
        $this->withHtml($secondPageRequest)->assertElementNotExists('div');

        $namePartial = substr($imgDetails['name'], 0, 3);
        $searchHitRequest = $this->get("/images/gallery?page=1&uploaded_to={$pageId}&search={$namePartial}");
        $searchHitRequest->assertSuccessful()->assertSee($imgDetails['name']);

        $namePartial = Str::random(16);
        $searchFailRequest = $this->get("/images/gallery?page=1&uploaded_to={$pageId}&search={$namePartial}");
        $searchFailRequest->assertSuccessful()->assertDontSee($imgDetails['name']);
        $searchFailRequest->assertSuccessful();
        $this->withHtml($searchFailRequest)->assertElementNotExists('div');
    }

    public function test_image_usage()
    {
        $page = Page::query()->first();
        $editor = $this->getEditor();
        $this->actingAs($editor);

        $imgDetails = $this->uploadGalleryImage($page);

        $image = Image::query()->first();
        $page->html = '<img src="' . $image->url . '">';
        $page->save();

        $usage = $this->get('/images/edit/' . $image->id . '?delete=true');
        $usage->assertSuccessful();
        $usage->assertSeeText($page->name);
        $usage->assertSee($page->getUrl());

        $this->deleteImage($imgDetails['path']);
    }

    public function test_php_files_cannot_be_uploaded()
    {
        $page = Page::query()->first();
        $admin = $this->getAdmin();
        $this->actingAs($admin);

        $fileName = 'bad.php';
        $relPath = $this->getTestImagePath('gallery', $fileName);
        $this->deleteImage($relPath);

        $file = $this->newTestImageFromBase64('bad-php.base64', $fileName);
        $upload = $this->withHeader('Content-Type', 'image/jpeg')->call('POST', '/images/gallery', ['uploaded_to' => $page->id], [], ['file' => $file], []);
        $upload->assertStatus(302);

        $this->assertFalse(file_exists(public_path($relPath)), 'Uploaded php file was uploaded but should have been stopped');

        $this->assertDatabaseMissing('images', [
            'type' => 'gallery',
            'name' => $fileName,
        ]);
    }

    public function test_php_like_files_cannot_be_uploaded()
    {
        $page = Page::query()->first();
        $admin = $this->getAdmin();
        $this->actingAs($admin);

        $fileName = 'bad.phtml';
        $relPath = $this->getTestImagePath('gallery', $fileName);
        $this->deleteImage($relPath);

        $file = $this->newTestImageFromBase64('bad-phtml.base64', $fileName);
        $upload = $this->withHeader('Content-Type', 'image/jpeg')->call('POST', '/images/gallery', ['uploaded_to' => $page->id], [], ['file' => $file], []);
        $upload->assertStatus(302);

        $this->assertFalse(file_exists(public_path($relPath)), 'Uploaded php file was uploaded but should have been stopped');
    }

    public function test_files_with_double_extensions_will_get_sanitized()
    {
        $page = Page::query()->first();
        $admin = $this->getAdmin();
        $this->actingAs($admin);

        $fileName = 'bad.phtml.png';
        $relPath = $this->getTestImagePath('gallery', $fileName);
        $expectedRelPath = dirname($relPath) . '/bad-phtml.png';
        $this->deleteImage($expectedRelPath);

        $file = $this->newTestImageFromBase64('bad-phtml-png.base64', $fileName);
        $upload = $this->withHeader('Content-Type', 'image/png')->call('POST', '/images/gallery', ['uploaded_to' => $page->id], [], ['file' => $file], []);
        $upload->assertStatus(200);

        $lastImage = Image::query()->latest('id')->first();

        $this->assertEquals('bad.phtml.png', $lastImage->name);
        $this->assertEquals('bad-phtml.png', basename($lastImage->path));
        $this->assertFileDoesNotExist(public_path($relPath), 'Uploaded image file name was not stripped of dots');
        $this->assertFileExists(public_path($expectedRelPath));

        $this->deleteImage($lastImage->path);
    }

    public function test_url_entities_removed_from_filenames()
    {
        $this->asEditor();
        $badNames = [
            'bad-char-#-image.png',
            'bad-char-?-image.png',
            '?#.png',
            '?.png',
            '#.png',
        ];
        foreach ($badNames as $name) {
            $galleryFile = $this->getTestImage($name);
            $page = Page::query()->first();
            $badPath = $this->getTestImagePath('gallery', $name);
            $this->deleteImage($badPath);

            $upload = $this->call('POST', '/images/gallery', ['uploaded_to' => $page->id], [], ['file' => $galleryFile], []);
            $upload->assertStatus(200);

            $lastImage = Image::query()->latest('id')->first();
            $newFileName = explode('.', basename($lastImage->path))[0];

            $this->assertEquals($lastImage->name, $name);
            $this->assertFalse(strpos($lastImage->path, $name), 'Path contains original image name');
            $this->assertFalse(file_exists(public_path($badPath)), 'Uploaded image file name was not stripped of url entities');

            $this->assertTrue(strlen($newFileName) > 0, 'File name was reduced to nothing');

            $this->deleteImage($lastImage->path);
        }
    }

    public function test_secure_images_uploads_to_correct_place()
    {
        config()->set('filesystems.images', 'local_secure');
        $this->asEditor();
        $galleryFile = $this->getTestImage('my-secure-test-upload.png');
        $page = Page::query()->first();
        $expectedPath = storage_path('uploads/images/gallery/' . date('Y-m') . '/my-secure-test-upload.png');

        $upload = $this->call('POST', '/images/gallery', ['uploaded_to' => $page->id], [], ['file' => $galleryFile], []);
        $upload->assertStatus(200);

        $this->assertTrue(file_exists($expectedPath), 'Uploaded image not found at path: ' . $expectedPath);

        if (file_exists($expectedPath)) {
            unlink($expectedPath);
        }
    }

    public function test_secure_image_paths_traversal_causes_500()
    {
        config()->set('filesystems.images', 'local_secure');
        $this->asEditor();

        $resp = $this->get('/uploads/images/../../logs/laravel.log');
        $resp->assertStatus(500);
    }

    public function test_secure_image_paths_traversal_on_non_secure_images_causes_404()
    {
        config()->set('filesystems.images', 'local');
        $this->asEditor();

        $resp = $this->get('/uploads/images/../../logs/laravel.log');
        $resp->assertStatus(404);
    }

    public function test_secure_image_paths_dont_serve_non_images()
    {
        config()->set('filesystems.images', 'local_secure');
        $this->asEditor();

        $testFilePath = storage_path('/uploads/images/testing.txt');
        file_put_contents($testFilePath, 'hello from test_secure_image_paths_dont_serve_non_images');

        $resp = $this->get('/uploads/images/testing.txt');
        $resp->assertStatus(404);
    }

    public function test_secure_images_included_in_exports()
    {
        config()->set('filesystems.images', 'local_secure');
        $this->asEditor();
        $galleryFile = $this->getTestImage('my-secure-test-upload.png');
        $page = Page::query()->first();
        $expectedPath = storage_path('uploads/images/gallery/' . date('Y-m') . '/my-secure-test-upload.png');

        $upload = $this->call('POST', '/images/gallery', ['uploaded_to' => $page->id], [], ['file' => $galleryFile], []);
        $imageUrl = json_decode($upload->getContent(), true)['url'];
        $page->html .= "<img src=\"{$imageUrl}\">";
        $page->save();
        $upload->assertStatus(200);

        $encodedImageContent = base64_encode(file_get_contents($expectedPath));
        $export = $this->get($page->getUrl('/export/html'));
        $this->assertTrue(strpos($export->getContent(), $encodedImageContent) !== false, 'Uploaded image in export content');

        if (file_exists($expectedPath)) {
            unlink($expectedPath);
        }
    }

    public function test_system_images_remain_public()
    {
        config()->set('filesystems.images', 'local_secure');
        $this->asAdmin();
        $galleryFile = $this->getTestImage('my-system-test-upload.png');
        $expectedPath = public_path('uploads/images/system/' . date('Y-m') . '/my-system-test-upload.png');

        $upload = $this->call('POST', '/settings/customization', [], [], ['app_logo' => $galleryFile], []);
        $upload->assertRedirect('/settings/customization');

        $this->assertTrue(file_exists($expectedPath), 'Uploaded image not found at path: ' . $expectedPath);

        if (file_exists($expectedPath)) {
            unlink($expectedPath);
        }
    }

    public function test_secure_restricted_images_inaccessible_without_relation_permission()
    {
        config()->set('filesystems.images', 'local_secure_restricted');
        $this->asEditor();
        $galleryFile = $this->getTestImage('my-secure-restricted-test-upload.png');
        /** @var Page $page */
        $page = Page::query()->first();

        $upload = $this->call('POST', '/images/gallery', ['uploaded_to' => $page->id], [], ['file' => $galleryFile], []);
        $upload->assertStatus(200);
        $expectedUrl = url('uploads/images/gallery/' . date('Y-m') . '/my-secure-restricted-test-upload.png');
        $expectedPath = storage_path('uploads/images/gallery/' . date('Y-m') . '/my-secure-restricted-test-upload.png');

        $this->get($expectedUrl)->assertOk();

        $this->entities->setPermissions($page, [], []);

        $resp = $this->get($expectedUrl);
        $resp->assertNotFound();

        if (file_exists($expectedPath)) {
            unlink($expectedPath);
        }
    }

    public function test_thumbnail_path_handled_by_secure_restricted_images()
    {
        config()->set('filesystems.images', 'local_secure_restricted');
        $this->asEditor();
        $galleryFile = $this->getTestImage('my-secure-restricted-thumb-test-test.png');
        /** @var Page $page */
        $page = Page::query()->first();

        $upload = $this->call('POST', '/images/gallery', ['uploaded_to' => $page->id], [], ['file' => $galleryFile], []);
        $upload->assertStatus(200);
        $expectedUrl = url('uploads/images/gallery/' . date('Y-m') . '/thumbs-150-150/my-secure-restricted-thumb-test-test.png');
        $expectedPath = storage_path('uploads/images/gallery/' . date('Y-m') . '/my-secure-restricted-thumb-test-test.png');

        $this->get($expectedUrl)->assertOk();

        $this->entities->setPermissions($page, [], []);

        $resp = $this->get($expectedUrl);
        $resp->assertNotFound();

        if (file_exists($expectedPath)) {
            unlink($expectedPath);
        }
    }

    public function test_secure_restricted_image_access_controlled_in_exports()
    {
        config()->set('filesystems.images', 'local_secure_restricted');
        $this->asEditor();
        $galleryFile = $this->getTestImage('my-secure-restricted-export-test.png');

        /** @var Page $pageA */
        /** @var Page $pageB */
        $pageA = Page::query()->first();
        $pageB = Page::query()->where('id', '!=', $pageA->id)->first();
        $expectedPath = storage_path('uploads/images/gallery/' . date('Y-m') . '/my-secure-restricted-export-test.png');

        $upload = $this->asEditor()->call('POST', '/images/gallery', ['uploaded_to' => $pageA->id], [], ['file' => $galleryFile], []);
        $upload->assertOk();

        $imageUrl = json_decode($upload->getContent(), true)['url'];
        $pageB->html .= "<img src=\"{$imageUrl}\">";
        $pageB->save();

        $encodedImageContent = base64_encode(file_get_contents($expectedPath));
        $export = $this->get($pageB->getUrl('/export/html'));
        $this->assertStringContainsString($encodedImageContent, $export->getContent());

        $this->entities->setPermissions($pageA, [], []);

        $export = $this->get($pageB->getUrl('/export/html'));
        $this->assertStringNotContainsString($encodedImageContent, $export->getContent());

        if (file_exists($expectedPath)) {
            unlink($expectedPath);
        }
    }

    public function test_image_delete()
    {
        $page = Page::query()->first();
        $this->asAdmin();
        $imageName = 'first-image.png';
        $relPath = $this->getTestImagePath('gallery', $imageName);
        $this->deleteImage($relPath);

        $this->uploadImage($imageName, $page->id);
        $image = Image::first();

        $delete = $this->delete('/images/' . $image->id);
        $delete->assertStatus(200);

        $this->assertDatabaseMissing('images', [
            'url'  => $this->baseUrl . $relPath,
            'type' => 'gallery',
        ]);

        $this->assertFalse(file_exists(public_path($relPath)), 'Uploaded image has not been deleted as expected');
    }

    public function test_image_delete_does_not_delete_similar_images()
    {
        $page = Page::query()->first();
        $this->asAdmin();
        $imageName = 'first-image.png';

        $relPath = $this->getTestImagePath('gallery', $imageName);
        $this->deleteImage($relPath);

        $this->uploadImage($imageName, $page->id);
        $this->uploadImage($imageName, $page->id);
        $this->uploadImage($imageName, $page->id);

        $image = Image::first();
        $folder = public_path(dirname($relPath));
        $imageCount = count(glob($folder . '/*'));

        $delete = $this->delete('/images/' . $image->id);
        $delete->assertStatus(200);

        $newCount = count(glob($folder . '/*'));
        $this->assertEquals($imageCount - 1, $newCount, 'More files than expected have been deleted');
        $this->assertFalse(file_exists(public_path($relPath)), 'Uploaded image has not been deleted as expected');
    }

    public function test_image_manager_delete_button_only_shows_with_permission()
    {
        $page = Page::query()->first();
        $this->asAdmin();
        $imageName = 'first-image.png';
        $relPath = $this->getTestImagePath('gallery', $imageName);
        $this->deleteImage($relPath);
        $viewer = $this->getViewer();

        $this->uploadImage($imageName, $page->id);
        $image = Image::first();

        $resp = $this->get("/images/edit/{$image->id}");
        $this->withHtml($resp)->assertElementExists('button#image-manager-delete[title="Delete"]');

        $resp = $this->actingAs($viewer)->get("/images/edit/{$image->id}");
        $this->withHtml($resp)->assertElementNotExists('button#image-manager-delete[title="Delete"]');

        $this->giveUserPermissions($viewer, ['image-delete-all']);

        $resp = $this->actingAs($viewer)->get("/images/edit/{$image->id}");
        $this->withHtml($resp)->assertElementExists('button#image-manager-delete[title="Delete"]');

        $this->deleteImage($relPath);
    }

    protected function getTestProfileImage()
    {
        $imageName = 'profile.png';
        $relPath = $this->getTestImagePath('user', $imageName);
        $this->deleteImage($relPath);

        return $this->getTestImage($imageName);
    }

    public function test_user_image_upload()
    {
        $editor = $this->getEditor();
        $admin = $this->getAdmin();
        $this->actingAs($admin);

        $file = $this->getTestProfileImage();
        $this->call('PUT', '/settings/users/' . $editor->id, [], [], ['profile_image' => $file], []);

        $this->assertDatabaseHas('images', [
            'type'        => 'user',
            'uploaded_to' => $editor->id,
            'created_by'  => $admin->id,
        ]);
    }

    public function test_user_images_deleted_on_user_deletion()
    {
        $editor = $this->getEditor();
        $this->actingAs($editor);

        $file = $this->getTestProfileImage();
        $this->call('PUT', '/settings/users/' . $editor->id, [], [], ['profile_image' => $file], []);

        $profileImages = Image::where('type', '=', 'user')->where('created_by', '=', $editor->id)->get();
        $this->assertTrue($profileImages->count() === 1, 'Found profile images does not match upload count');

        $imagePath = public_path($profileImages->first()->path);
        $this->assertTrue(file_exists($imagePath));

        $userDelete = $this->asAdmin()->delete("/settings/users/{$editor->id}");
        $userDelete->assertStatus(302);

        $this->assertDatabaseMissing('images', [
            'type'       => 'user',
            'created_by' => $editor->id,
        ]);
        $this->assertDatabaseMissing('images', [
            'type'        => 'user',
            'uploaded_to' => $editor->id,
        ]);

        $this->assertFalse(file_exists($imagePath));
    }

    public function test_deleted_unused_images()
    {
        $page = Page::query()->first();
        $admin = $this->getAdmin();
        $this->actingAs($admin);

        $imageName = 'unused-image.png';
        $relPath = $this->getTestImagePath('gallery', $imageName);
        $this->deleteImage($relPath);

        $upload = $this->uploadImage($imageName, $page->id);
        $upload->assertStatus(200);
        $image = Image::where('type', '=', 'gallery')->first();

        $pageRepo = app(PageRepo::class);
        $pageRepo->update($page, [
            'name'    => $page->name,
            'html'    => $page->html . "<img src=\"{$image->url}\">",
            'summary' => '',
        ]);

        // Ensure no images are reported as deletable
        $imageService = app(ImageService::class);
        $toDelete = $imageService->deleteUnusedImages(true, true);
        $this->assertCount(0, $toDelete);

        // Save a revision of our page without the image;
        $pageRepo->update($page, [
            'name'    => $page->name,
            'html'    => '<p>Hello</p>',
            'summary' => '',
        ]);

        // Ensure revision images are picked up okay
        $imageService = app(ImageService::class);
        $toDelete = $imageService->deleteUnusedImages(true, true);
        $this->assertCount(0, $toDelete);
        $toDelete = $imageService->deleteUnusedImages(false, true);
        $this->assertCount(1, $toDelete);

        // Check image is found when revisions are destroyed
        $page->revisions()->delete();
        $toDelete = $imageService->deleteUnusedImages(true, true);
        $this->assertCount(1, $toDelete);

        // Check the image is deleted
        $absPath = public_path($relPath);
        $this->assertTrue(file_exists($absPath), "Existing uploaded file at path {$absPath} exists");
        $toDelete = $imageService->deleteUnusedImages(true, false);
        $this->assertCount(1, $toDelete);
        $this->assertFalse(file_exists($absPath));

        $this->deleteImage($relPath);
    }
}
