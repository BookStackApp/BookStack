<?php

namespace Tests\Exports;

use BookStack\Activity\ActivityType;
use BookStack\Exports\Import;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use ZipArchive;

class ZipImportTest extends TestCase
{
    public function test_import_page_view()
    {
        $resp = $this->asAdmin()->get('/import');
        $resp->assertSee('Import');
        $this->withHtml($resp)->assertElementExists('form input[type="file"][name="file"]');
    }

    public function test_permissions_needed_for_import_page()
    {
        $user = $this->users->viewer();
        $this->actingAs($user);

        $resp = $this->get('/books');
        $this->withHtml($resp)->assertLinkNotExists(url('/import'));
        $resp = $this->get('/import');
        $resp->assertRedirect('/');

        $this->permissions->grantUserRolePermissions($user, ['content-import']);

        $resp = $this->get('/books');
        $this->withHtml($resp)->assertLinkExists(url('/import'));
        $resp = $this->get('/import');
        $resp->assertOk();
        $resp->assertSeeText('Select ZIP file to upload');
    }

    public function test_import_page_pending_import_visibility_limited()
    {
        $user = $this->users->viewer();
        $admin = $this->users->admin();
        $userImport = Import::factory()->create(['name' => 'MySuperUserImport', 'created_by' => $user->id]);
        $adminImport = Import::factory()->create(['name' => 'MySuperAdminImport', 'created_by' => $admin->id]);
        $this->permissions->grantUserRolePermissions($user, ['content-import']);

        $resp = $this->actingAs($user)->get('/import');
        $resp->assertSeeText('MySuperUserImport');
        $resp->assertDontSeeText('MySuperAdminImport');

        $this->permissions->grantUserRolePermissions($user, ['settings-manage']);

        $resp = $this->actingAs($user)->get('/import');
        $resp->assertSeeText('MySuperUserImport');
        $resp->assertSeeText('MySuperAdminImport');
    }

    public function test_zip_read_errors_are_shown_on_validation()
    {
        $invalidUpload = $this->files->uploadedImage('image.zip');

        $this->asAdmin();
        $resp = $this->runImportFromFile($invalidUpload);
        $resp->assertRedirect('/import');

        $resp = $this->followRedirects($resp);
        $resp->assertSeeText('Could not read ZIP file');
    }

    public function test_error_shown_if_missing_data()
    {
        $zipFile = tempnam(sys_get_temp_dir(), 'bstest-');
        $zip = new ZipArchive();
        $zip->open($zipFile, ZipArchive::CREATE);
        $zip->addFromString('beans', 'cat');
        $zip->close();

        $this->asAdmin();
        $upload = new UploadedFile($zipFile, 'upload.zip', 'application/zip', null, true);
        $resp = $this->runImportFromFile($upload);
        $resp->assertRedirect('/import');

        $resp = $this->followRedirects($resp);
        $resp->assertSeeText('Could not find and decode ZIP data.json content.');
    }

    public function test_error_shown_if_no_importable_key()
    {
        $this->asAdmin();
        $resp = $this->runImportFromFile($this->zipUploadFromData([
            'instance' => []
        ]));

        $resp->assertRedirect('/import');
        $resp = $this->followRedirects($resp);
        $resp->assertSeeText('ZIP file data has no expected book, chapter or page content.');
    }

    public function test_zip_data_validation_messages_shown()
    {
        $this->asAdmin();
        $resp = $this->runImportFromFile($this->zipUploadFromData([
            'book' => [
                'id' => 4,
                'pages' => [
                    'cat',
                    [
                        'name' => 'My inner page',
                        'tags' => [
                            [
                                'value' => 5
                            ]
                        ],
                    ]
                ],
            ]
        ]));

        $resp->assertRedirect('/import');
        $resp = $this->followRedirects($resp);

        $resp->assertSeeText('[book.name]: The name field is required.');
        $resp->assertSeeText('[book.pages.0.0]: Data object expected but "string" found.');
        $resp->assertSeeText('[book.pages.1.tags.0.name]: The name field is required.');
        $resp->assertSeeText('[book.pages.1.tags.0.value]: The value must be a string.');
    }

    public function test_import_upload_success()
    {
        $admin = $this->users->admin();
        $this->actingAs($admin);
        $resp = $this->runImportFromFile($this->zipUploadFromData([
            'book' => [
                'name' => 'My great book name',
                'chapters' => [
                    [
                        'name' => 'my chapter',
                        'pages' => [
                            [
                                'name' => 'my chapter page',
                            ]
                        ]
                    ]
                ],
                'pages' => [
                    [
                        'name' => 'My page',
                    ]
                ],
            ],
        ]));

        $this->assertDatabaseHas('imports', [
            'name' => 'My great book name',
            'book_count' => 1,
            'chapter_count' => 1,
            'page_count' => 2,
            'created_by' => $admin->id,
        ]);

        /** @var Import $import */
        $import = Import::query()->latest()->first();
        $resp->assertRedirect("/import/{$import->id}");
        $this->assertFileExists(storage_path($import->path));
        $this->assertActivityExists(ActivityType::IMPORT_CREATE);
    }

    public function test_import_show_page()
    {
        $import = Import::factory()->create(['name' => 'MySuperAdminImport']);

        $resp = $this->asAdmin()->get("/import/{$import->id}");
        $resp->assertOk();
        $resp->assertSee('MySuperAdminImport');
    }

    public function test_import_show_page_access_limited()
    {
        $user = $this->users->viewer();
        $admin = $this->users->admin();
        $userImport = Import::factory()->create(['name' => 'MySuperUserImport', 'created_by' => $user->id]);
        $adminImport = Import::factory()->create(['name' => 'MySuperAdminImport', 'created_by' => $admin->id]);
        $this->actingAs($user);

        $this->get("/import/{$userImport->id}")->assertRedirect('/');
        $this->get("/import/{$adminImport->id}")->assertRedirect('/');

        $this->permissions->grantUserRolePermissions($user, ['content-import']);

        $this->get("/import/{$userImport->id}")->assertOk();
        $this->get("/import/{$adminImport->id}")->assertStatus(404);

        $this->permissions->grantUserRolePermissions($user, ['settings-manage']);

        $this->get("/import/{$userImport->id}")->assertOk();
        $this->get("/import/{$adminImport->id}")->assertOk();
    }

    public function test_import_delete()
    {
        $this->asAdmin();
        $this->runImportFromFile($this->zipUploadFromData([
            'book' => [
                'name' => 'My great book name'
            ],
        ]));

        /** @var Import $import */
        $import = Import::query()->latest()->first();
        $this->assertDatabaseHas('imports', [
            'id' => $import->id,
            'name' => 'My great book name'
        ]);
        $this->assertFileExists(storage_path($import->path));

        $resp = $this->delete("/import/{$import->id}");

        $resp->assertRedirect('/import');
        $this->assertActivityExists(ActivityType::IMPORT_DELETE);
        $this->assertDatabaseMissing('imports', [
            'id' => $import->id,
        ]);
        $this->assertFileDoesNotExist(storage_path($import->path));
    }

    public function test_import_delete_access_limited()
    {
        $user = $this->users->viewer();
        $admin = $this->users->admin();
        $userImport = Import::factory()->create(['name' => 'MySuperUserImport', 'created_by' => $user->id]);
        $adminImport = Import::factory()->create(['name' => 'MySuperAdminImport', 'created_by' => $admin->id]);
        $this->actingAs($user);

        $this->delete("/import/{$userImport->id}")->assertRedirect('/');
        $this->delete("/import/{$adminImport->id}")->assertRedirect('/');

        $this->permissions->grantUserRolePermissions($user, ['content-import']);

        $this->delete("/import/{$userImport->id}")->assertRedirect('/import');
        $this->delete("/import/{$adminImport->id}")->assertStatus(404);

        $this->permissions->grantUserRolePermissions($user, ['settings-manage']);

        $this->delete("/import/{$adminImport->id}")->assertRedirect('/import');
    }

    protected function runImportFromFile(UploadedFile $file): TestResponse
    {
        return $this->call('POST', '/import', [], [], ['file' => $file]);
    }

    protected function zipUploadFromData(array $data): UploadedFile
    {
        $zipFile = tempnam(sys_get_temp_dir(), 'bstest-');

        $zip = new ZipArchive();
        $zip->open($zipFile, ZipArchive::CREATE);
        $zip->addFromString('data.json', json_encode($data));
        $zip->close();

        return new UploadedFile($zipFile, 'upload.zip', 'application/zip', null, true);
    }
}
