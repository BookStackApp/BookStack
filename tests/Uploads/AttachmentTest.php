<?php

namespace Tests\Uploads;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Entities\Tools\TrashCan;
use BookStack\Uploads\Attachment;
use Tests\TestCase;

class AttachmentTest extends TestCase
{
    public function test_file_upload()
    {
        $page = $this->entities->page();
        $this->asAdmin();
        $admin = $this->users->admin();
        $fileName = 'upload_test_file.txt';

        $expectedResp = [
            'name'       => $fileName,
            'uploaded_to' => $page->id,
            'extension'  => 'txt',
            'order'      => 1,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ];

        $upload = $this->files->uploadAttachmentFile($this, $fileName, $page->id);
        $upload->assertStatus(200);

        $attachment = Attachment::query()->orderBy('id', 'desc')->first();
        $upload->assertJson($expectedResp);

        $expectedResp['path'] = $attachment->path;
        $this->assertDatabaseHas('attachments', $expectedResp);

        $this->files->deleteAllAttachmentFiles();
    }

    public function test_file_upload_does_not_use_filename()
    {
        $page = $this->entities->page();
        $fileName = 'upload_test_file.txt';

        $this->asAdmin();
        $upload = $this->files->uploadAttachmentFile($this, $fileName, $page->id);
        $upload->assertStatus(200);

        $attachment = Attachment::query()->orderBy('id', 'desc')->first();
        $this->assertStringNotContainsString($fileName, $attachment->path);
        $this->assertStringEndsWith('-txt', $attachment->path);
        $this->files->deleteAllAttachmentFiles();
    }

    public function test_file_display_and_access()
    {
        $page = $this->entities->page();
        $this->asAdmin();
        $fileName = 'upload_test_file.txt';

        $upload = $this->files->uploadAttachmentFile($this, $fileName, $page->id);
        $upload->assertStatus(200);
        $attachment = Attachment::orderBy('id', 'desc')->take(1)->first();

        $pageGet = $this->get($page->getUrl());
        $pageGet->assertSeeText($fileName);
        $pageGet->assertSee($attachment->getUrl());

        $attachmentGet = $this->get($attachment->getUrl());
        $content = $attachmentGet->streamedContent();
        $this->assertStringContainsString('Hi, This is a test file for testing the upload process.', $content);

        $this->files->deleteAllAttachmentFiles();
    }

    public function test_attaching_link_to_page()
    {
        $page = $this->entities->page();
        $admin = $this->users->admin();
        $this->asAdmin();

        $linkReq = $this->call('POST', 'attachments/link', [
            'attachment_link_url'         => 'https://example.com',
            'attachment_link_name'        => 'Example Attachment Link',
            'attachment_link_uploaded_to' => $page->id,
        ]);

        $expectedData = [
            'path'        => 'https://example.com',
            'name'        => 'Example Attachment Link',
            'uploaded_to' => $page->id,
            'created_by'  => $admin->id,
            'updated_by'  => $admin->id,
            'external'    => true,
            'order'       => 1,
            'extension'   => '',
        ];

        $linkReq->assertStatus(200);
        $this->assertDatabaseHas('attachments', $expectedData);
        $attachment = Attachment::orderBy('id', 'desc')->take(1)->first();

        $pageGet = $this->get($page->getUrl());
        $pageGet->assertSeeText('Example Attachment Link');
        $pageGet->assertSee($attachment->getUrl());

        $attachmentGet = $this->get($attachment->getUrl());
        $attachmentGet->assertRedirect('https://example.com');

        $this->files->deleteAllAttachmentFiles();
    }

    public function test_attachment_updating()
    {
        $page = $this->entities->page();
        $this->asAdmin();

        $attachment = Attachment::factory()->create(['uploaded_to' => $page->id]);
        $update = $this->call('PUT', 'attachments/' . $attachment->id, [
            'attachment_edit_name' => 'My new attachment name',
            'attachment_edit_url'  => 'https://test.example.com',
        ]);

        $expectedData = [
            'id'          => $attachment->id,
            'path'        => 'https://test.example.com',
            'name'        => 'My new attachment name',
            'uploaded_to' => $page->id,
        ];

        $update->assertStatus(200);
        $this->assertDatabaseHas('attachments', $expectedData);

        $this->files->deleteAllAttachmentFiles();
    }

    public function test_file_deletion()
    {
        $page = $this->entities->page();
        $this->asAdmin();
        $fileName = 'deletion_test.txt';
        $this->files->uploadAttachmentFile($this, $fileName, $page->id);

        $attachment = Attachment::query()->orderBy('id', 'desc')->first();
        $filePath = storage_path($attachment->path);
        $this->assertTrue(file_exists($filePath), 'File at path ' . $filePath . ' does not exist');

        $attachment = Attachment::first();
        $this->delete($attachment->getUrl());

        $this->assertDatabaseMissing('attachments', [
            'name' => $fileName,
        ]);
        $this->assertFalse(file_exists($filePath), 'File at path ' . $filePath . ' was not deleted as expected');

        $this->files->deleteAllAttachmentFiles();
    }

    public function test_attachment_deletion_on_page_deletion()
    {
        $page = $this->entities->page();
        $this->asAdmin();
        $fileName = 'deletion_test.txt';
        $this->files->uploadAttachmentFile($this, $fileName, $page->id);

        $attachment = Attachment::query()->orderBy('id', 'desc')->first();
        $filePath = storage_path($attachment->path);

        $this->assertTrue(file_exists($filePath), 'File at path ' . $filePath . ' does not exist');
        $this->assertDatabaseHas('attachments', [
            'name' => $fileName,
        ]);

        app(PageRepo::class)->destroy($page);
        app(TrashCan::class)->empty();

        $this->assertDatabaseMissing('attachments', [
            'name' => $fileName,
        ]);
        $this->assertFalse(file_exists($filePath), 'File at path ' . $filePath . ' was not deleted as expected');

        $this->files->deleteAllAttachmentFiles();
    }

    public function test_attachment_access_without_permission_shows_404()
    {
        $admin = $this->users->admin();
        $viewer = $this->users->viewer();
        $page = $this->entities->page(); /** @var Page $page */
        $this->actingAs($admin);
        $fileName = 'permission_test.txt';
        $this->files->uploadAttachmentFile($this, $fileName, $page->id);
        $attachment = Attachment::orderBy('id', 'desc')->take(1)->first();

        $this->permissions->setEntityPermissions($page, [], []);

        $this->actingAs($viewer);
        $attachmentGet = $this->get($attachment->getUrl());
        $attachmentGet->assertStatus(404);
        $attachmentGet->assertSee('Attachment not found');

        $this->files->deleteAllAttachmentFiles();
    }

    public function test_data_and_js_links_cannot_be_attached_to_a_page()
    {
        $page = $this->entities->page();
        $this->asAdmin();

        $badLinks = [
            'javascript:alert("bunny")',
            ' javascript:alert("bunny")',
            'JavaScript:alert("bunny")',
            "\t\n\t\nJavaScript:alert(\"bunny\")",
            'data:text/html;<a></a>',
            'Data:text/html;<a></a>',
            'Data:text/html;<a></a>',
        ];

        foreach ($badLinks as $badLink) {
            $linkReq = $this->post('attachments/link', [
                'attachment_link_url'         => $badLink,
                'attachment_link_name'        => 'Example Attachment Link',
                'attachment_link_uploaded_to' => $page->id,
            ]);
            $linkReq->assertStatus(422);
            $this->assertDatabaseMissing('attachments', [
                'path' => $badLink,
            ]);
        }

        $attachment = Attachment::factory()->create(['uploaded_to' => $page->id]);

        foreach ($badLinks as $badLink) {
            $linkReq = $this->put('attachments/' . $attachment->id, [
                'attachment_edit_url'  => $badLink,
                'attachment_edit_name' => 'Example Attachment Link',
            ]);
            $linkReq->assertStatus(422);
            $this->assertDatabaseMissing('attachments', [
                'path' => $badLink,
            ]);
        }
    }

    public function test_file_access_with_open_query_param_provides_inline_response_with_correct_content_type()
    {
        $page = $this->entities->page();
        $this->asAdmin();
        $fileName = 'upload_test_file.txt';

        $upload = $this->files->uploadAttachmentFile($this, $fileName, $page->id);
        $upload->assertStatus(200);
        $attachment = Attachment::query()->orderBy('id', 'desc')->take(1)->first();

        $attachmentGet = $this->get($attachment->getUrl(true));
        // http-foundation/Response does some 'fixing' of responses to add charsets to text responses.
        $attachmentGet->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $attachmentGet->assertHeader('Content-Disposition', 'inline; filename="upload_test_file.txt"');
        $attachmentGet->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->files->deleteAllAttachmentFiles();
    }

    public function test_html_file_access_with_open_forces_plain_content_type()
    {
        $page = $this->entities->page();
        $this->asAdmin();

        $attachment = $this->files->uploadAttachmentDataToPage($this, $page, 'test_file.html', '<html></html><p>testing</p>', 'text/html');

        $attachmentGet = $this->get($attachment->getUrl(true));
        // http-foundation/Response does some 'fixing' of responses to add charsets to text responses.
        $attachmentGet->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $attachmentGet->assertHeader('Content-Disposition', 'inline; filename="test_file.html"');

        $this->files->deleteAllAttachmentFiles();
    }

    public function test_file_upload_works_when_local_secure_restricted_is_in_use()
    {
        config()->set('filesystems.attachments', 'local_secure_restricted');

        $page = $this->entities->page();
        $fileName = 'upload_test_file.txt';

        $this->asAdmin();
        $upload = $this->files->uploadAttachmentFile($this, $fileName, $page->id);
        $upload->assertStatus(200);

        $attachment = Attachment::query()->orderBy('id', 'desc')->where('uploaded_to', '=', $page->id)->first();
        $this->assertFileExists(storage_path($attachment->path));
        $this->files->deleteAllAttachmentFiles();
    }
}
