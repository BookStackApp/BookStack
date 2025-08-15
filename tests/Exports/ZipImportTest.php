<?php

namespace Tests\Exports;

use BookStack\Activity\ActivityType;
use BookStack\Entities\Models\Book;
use BookStack\Exports\Import;
use BookStack\Exports\ZipExports\Models\ZipExportBook;
use BookStack\Exports\ZipExports\Models\ZipExportChapter;
use BookStack\Exports\ZipExports\Models\ZipExportPage;
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
        $zip->open($zipFile, ZipArchive::OVERWRITE);
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
        $resp = $this->runImportFromFile(ZipTestHelper::zipUploadFromData([
            'instance' => []
        ]));

        $resp->assertRedirect('/import');
        $resp = $this->followRedirects($resp);
        $resp->assertSeeText('ZIP file data has no expected book, chapter or page content.');
    }

    public function test_zip_data_validation_messages_shown()
    {
        $this->asAdmin();
        $resp = $this->runImportFromFile(ZipTestHelper::zipUploadFromData([
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
        $data = [
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
        ];

        $resp = $this->runImportFromFile(ZipTestHelper::zipUploadFromData($data));

        $this->assertDatabaseHas('imports', [
            'name' => 'My great book name',
            'type' => 'book',
            'created_by' => $admin->id,
        ]);

        /** @var Import $import */
        $import = Import::query()->latest()->first();
        $resp->assertRedirect("/import/{$import->id}");
        $this->assertFileExists(storage_path($import->path));
        $this->assertActivityExists(ActivityType::IMPORT_CREATE);

        ZipTestHelper::deleteZipForImport($import);
    }

    public function test_import_show_page()
    {
        $exportBook = new ZipExportBook();
        $exportBook->name = 'My exported book';
        $exportChapter = new ZipExportChapter();
        $exportChapter->name = 'My exported chapter';
        $exportPage = new ZipExportPage();
        $exportPage->name = 'My exported page';
        $exportBook->chapters = [$exportChapter];
        $exportChapter->pages = [$exportPage];

        $import = Import::factory()->create([
            'name' => 'MySuperAdminImport',
            'metadata' => json_encode($exportBook)
        ]);

        $resp = $this->asAdmin()->get("/import/{$import->id}");
        $resp->assertOk();
        $resp->assertSeeText('My exported book');
        $resp->assertSeeText('My exported chapter');
        $resp->assertSeeText('My exported page');
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
        $this->runImportFromFile(ZipTestHelper::zipUploadFromData([
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

    public function test_run_simple_success_scenario()
    {
        $import = ZipTestHelper::importFromData([], [
            'book' => [
                'name' => 'My imported book',
                'pages' => [
                    [
                        'name' => 'My imported book page',
                        'html' => '<p>Hello there from child page!</p>'
                    ]
                ],
            ]
        ]);

        $resp = $this->asAdmin()->post("/import/{$import->id}");
        $book = Book::query()->where('name', '=', 'My imported book')->latest()->first();
        $resp->assertRedirect($book->getUrl());

        $resp = $this->followRedirects($resp);
        $resp->assertSee('My imported book page');
        $resp->assertSee('Hello there from child page!');

        $this->assertDatabaseMissing('imports', ['id' => $import->id]);
        $this->assertFileDoesNotExist(storage_path($import->path));
        $this->assertActivityExists(ActivityType::IMPORT_RUN, null, $import->logDescriptor());
    }

    public function test_import_run_access_limited()
    {
        $user = $this->users->editor();
        $admin = $this->users->admin();
        $userImport = Import::factory()->create(['name' => 'MySuperUserImport', 'created_by' => $user->id]);
        $adminImport = Import::factory()->create(['name' => 'MySuperAdminImport', 'created_by' => $admin->id]);
        $this->actingAs($user);

        $this->post("/import/{$userImport->id}")->assertRedirect('/');
        $this->post("/import/{$adminImport->id}")->assertRedirect('/');

        $this->permissions->grantUserRolePermissions($user, ['content-import']);

        $this->post("/import/{$userImport->id}")->assertRedirect($userImport->getUrl()); // Getting validation response instead of access issue response
        $this->post("/import/{$adminImport->id}")->assertStatus(404);

        $this->permissions->grantUserRolePermissions($user, ['settings-manage']);

        $this->post("/import/{$adminImport->id}")->assertRedirect($adminImport->getUrl()); // Getting validation response instead of access issue response
    }

    public function test_run_revalidates_content()
    {
        $import = ZipTestHelper::importFromData([], [
            'book' => [
                'id' => 'abc',
            ]
        ]);

        $resp = $this->asAdmin()->post("/import/{$import->id}");
        $resp->assertRedirect($import->getUrl());

        $resp = $this->followRedirects($resp);
        $resp->assertSeeText('The name field is required.');
        $resp->assertSeeText('The id must be an integer.');

        ZipTestHelper::deleteZipForImport($import);
    }

    public function test_run_checks_permissions_on_import()
    {
        $viewer = $this->users->viewer();
        $this->permissions->grantUserRolePermissions($viewer, ['content-import']);
        $import = ZipTestHelper::importFromData(['created_by' => $viewer->id], [
            'book' => ['name' => 'My import book'],
        ]);

        $resp = $this->asViewer()->post("/import/{$import->id}");
        $resp->assertRedirect($import->getUrl());

        $resp = $this->followRedirects($resp);
        $resp->assertSeeText('You are lacking the required permissions to create books.');

        ZipTestHelper::deleteZipForImport($import);
    }

    public function test_run_requires_parent_for_chapter_and_page_imports()
    {
        $book = $this->entities->book();
        $pageImport = ZipTestHelper::importFromData([], [
            'page' => ['name' => 'My page', 'html' => '<p>page test!</p>'],
        ]);
        $chapterImport = ZipTestHelper::importFromData([], [
            'chapter' => ['name' => 'My chapter'],
        ]);

        $resp = $this->asAdmin()->post("/import/{$pageImport->id}");
        $resp->assertRedirect($pageImport->getUrl());
        $this->followRedirects($resp)->assertSee('The parent field is required.');

        $resp = $this->asAdmin()->post("/import/{$pageImport->id}", ['parent' => "book:{$book->id}"]);
        $resp->assertRedirectContains($book->getUrl());

        $resp = $this->asAdmin()->post("/import/{$chapterImport->id}");
        $resp->assertRedirect($chapterImport->getUrl());
        $this->followRedirects($resp)->assertSee('The parent field is required.');

        $resp = $this->asAdmin()->post("/import/{$chapterImport->id}", ['parent' => "book:{$book->id}"]);
        $resp->assertRedirectContains($book->getUrl());
    }

    public function test_run_validates_correct_parent_type()
    {
        $chapter = $this->entities->chapter();
        $import = ZipTestHelper::importFromData([], [
            'chapter' => ['name' => 'My chapter'],
        ]);

        $resp = $this->asAdmin()->post("/import/{$import->id}", ['parent' => "chapter:{$chapter->id}"]);
        $resp->assertRedirect($import->getUrl());

        $resp = $this->followRedirects($resp);
        $resp->assertSee('Parent book required for chapter import.');

        ZipTestHelper::deleteZipForImport($import);
    }

    protected function runImportFromFile(UploadedFile $file): TestResponse
    {
        return $this->call('POST', '/import', [], [], ['file' => $file]);
    }
}
