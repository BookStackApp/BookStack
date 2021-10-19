<?php

namespace Tests\Api;

use BookStack\Entities\Models\Page;
use BookStack\Uploads\Attachment;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AttachmentsApiTest extends TestCase
{
    use TestsApi;

    protected $baseEndpoint = '/api/attachments';

    public function test_index_endpoint_returns_expected_book()
    {
        $this->actingAsApiEditor();
        $page = Page::query()->first();
        $attachment = $this->createAttachmentForPage($page, [
            'name' => 'My test attachment',
            'external' => true,
        ]);

        $resp = $this->getJson($this->baseEndpoint . '?count=1&sort=+id');
        $resp->assertJson(['data' => [
            [
                'id' => $attachment->id,
                'name' => 'My test attachment',
                'uploaded_to' => $page->id,
                'external' => true,
            ],
        ]]);
    }

    public function test_attachments_listing_based_upon_page_visibility()
    {
        $this->actingAsApiEditor();
        /** @var Page $page */
        $page = Page::query()->first();
        $attachment = $this->createAttachmentForPage($page, [
            'name' => 'My test attachment',
            'external' => true,
        ]);

        $resp = $this->getJson($this->baseEndpoint . '?count=1&sort=+id');
        $resp->assertJson(['data' => [
            [
                'id' => $attachment->id,
            ],
        ]]);

        $page->restricted = true;
        $page->save();
        $this->regenEntityPermissions($page);

        $resp = $this->getJson($this->baseEndpoint . '?count=1&sort=+id');
        $resp->assertJsonMissing(['data' => [
            [
                'id' => $attachment->id,
            ],
        ]]);
    }

    public function test_create_endpoint_for_link_attachment()
    {
        $this->actingAsApiAdmin();
        /** @var Page $page */
        $page = Page::query()->first();

        $details = [
            'name' => 'My attachment',
            'uploaded_to' => $page->id,
            'link' => 'https://cats.example.com',
        ];

        $resp = $this->postJson($this->baseEndpoint, $details);
        $resp->assertStatus(200);
        /** @var Attachment $newItem */
        $newItem = Attachment::query()->orderByDesc('id')->where('name', '=', $details['name'])->first();
        $resp->assertJson(['id' => $newItem->id, 'external' => true, 'name' => $details['name'], 'uploaded_to' => $page->id]);
    }

    public function test_create_endpoint_for_upload_attachment()
    {
        $this->actingAsApiAdmin();
        /** @var Page $page */
        $page = Page::query()->first();
        $file = $this->getTestFile('textfile.txt');

        $details = [
            'name' => 'My attachment',
            'uploaded_to' => $page->id,
        ];

        $resp = $this->call('POST', $this->baseEndpoint, $details, [], ['file' => $file]);
        $resp->assertStatus(200);
        /** @var Attachment $newItem */
        $newItem = Attachment::query()->orderByDesc('id')->where('name', '=', $details['name'])->first();
        $resp->assertJson(['id' => $newItem->id, 'external' => false, 'extension' => 'txt', 'name' => $details['name'], 'uploaded_to' => $page->id]);
        $this->assertTrue(file_exists(storage_path($newItem->path)));
        unlink(storage_path($newItem->path));
    }

    public function test_name_needed_to_create()
    {
        $this->actingAsApiAdmin();
        /** @var Page $page */
        $page = Page::query()->first();

        $details = [
            'uploaded_to' => $page->id,
            'link' => 'https://example.com',
        ];

        $resp = $this->postJson($this->baseEndpoint, $details);
        $resp->assertStatus(422);
        $resp->assertJson([
            'error' => [
                'message' => 'The given data was invalid.',
                'validation' => [
                    'name' => ['The name field is required.'],
                ],
                'code' => 422,
            ],
        ]);
    }

    public function test_link_or_file_needed_to_create()
    {
        $this->actingAsApiAdmin();
        /** @var Page $page */
        $page = Page::query()->first();

        $details = [
            'name' => 'my attachment',
            'uploaded_to' => $page->id,
        ];

        $resp = $this->postJson($this->baseEndpoint, $details);
        $resp->assertStatus(422);
        $resp->assertJson([
            'error' => [
                'message' => 'The given data was invalid.',
                'validation' => [
                    "file" => ["The file field is required when link is not present."],
                    "link" => ["The link field is required when file is not present."],
                ],
                'code' => 422,
            ],
        ]);
    }

    public function test_read_endpoint_for_link_attachment()
    {
        $this->actingAsApiAdmin();
        /** @var Page $page */
        $page = Page::query()->first();

        $attachment = $this->createAttachmentForPage($page, [
            'name' => 'my attachment',
            'path' => 'https://example.com',
            'order' => 1,
        ]);

        $resp = $this->getJson("{$this->baseEndpoint}/{$attachment->id}");

        $resp->assertStatus(200);
        $resp->assertJson([
            'id' => $attachment->id,
            'content' => 'https://example.com',
            'external' => true,
            'uploaded_to' => $page->id,
            'order' => 1,
            'created_by' => [
                'name' => $attachment->createdBy->name,
            ],
            'updated_by' => [
                'name' => $attachment->createdBy->name,
            ],
            'links' => [
                "html" => "<a target=\"_blank\" href=\"http://localhost/attachments/{$attachment->id}\">my attachment</a>",
                "markdown" => "[my attachment](http://localhost/attachments/{$attachment->id})"
            ],
        ]);
    }

    public function test_read_endpoint_for_file_attachment()
    {
        $this->actingAsApiAdmin();
        /** @var Page $page */
        $page = Page::query()->first();
        $file = $this->getTestFile('textfile.txt');

        $details = [
            'name' => 'My file attachment',
            'uploaded_to' => $page->id,
        ];
        $this->call('POST', $this->baseEndpoint, $details, [], ['file' => $file]);
        /** @var Attachment $attachment */
        $attachment = Attachment::query()->orderByDesc('id')->where('name', '=', $details['name'])->firstOrFail();

        $resp = $this->getJson("{$this->baseEndpoint}/{$attachment->id}");

        $resp->assertStatus(200);
        $resp->assertJson([
            'id' => $attachment->id,
            'content' => base64_encode(file_get_contents(storage_path($attachment->path))),
            'external' => false,
            'uploaded_to' => $page->id,
            'order' => 1,
            'created_by' => [
                'name' => $attachment->createdBy->name,
            ],
            'updated_by' => [
                'name' => $attachment->updatedBy->name,
            ],
            'links' => [
                "html" => "<a target=\"_blank\" href=\"http://localhost/attachments/{$attachment->id}\">My file attachment</a>",
                "markdown" => "[My file attachment](http://localhost/attachments/{$attachment->id})"
            ],
        ]);

        unlink(storage_path($attachment->path));
    }

    public function test_update_endpoint()
    {
        $this->actingAsApiAdmin();
        /** @var Page $page */
        $page = Page::query()->first();
        $attachment = $this->createAttachmentForPage($page);

        $details = [
            'name' => 'My updated API attachment',
        ];

        $resp = $this->putJson("{$this->baseEndpoint}/{$attachment->id}", $details);
        $attachment->refresh();

        $resp->assertStatus(200);
        $resp->assertJson(['id' => $attachment->id, 'name' => 'My updated API attachment']);
    }

    public function test_update_link_attachment_to_file()
    {
        $this->actingAsApiAdmin();
        /** @var Page $page */
        $page = Page::query()->first();
        $attachment = $this->createAttachmentForPage($page);
        $file = $this->getTestFile('textfile.txt');


        $resp = $this->call('PUT', "{$this->baseEndpoint}/{$attachment->id}", ['name' => 'My updated file'], [], ['file' => $file]);
        $resp->assertStatus(200);

        $attachment->refresh();
        $this->assertFalse($attachment->external);
        $this->assertEquals('txt', $attachment->extension);
        $this->assertStringStartsWith('uploads/files/', $attachment->path);
        $this->assertFileExists(storage_path($attachment->path));

        unlink(storage_path($attachment->path));
    }

    public function test_update_file_attachment_to_link()
    {
        $this->actingAsApiAdmin();
        /** @var Page $page */
        $page = Page::query()->first();
        $file = $this->getTestFile('textfile.txt');
        $this->call('POST', $this->baseEndpoint, ['name' => 'My file attachment', 'uploaded_to' => $page->id], [], ['file' => $file]);
        /** @var Attachment $attachment */
        $attachment = Attachment::query()->where('name', '=', 'My file attachment')->firstOrFail();

        $filePath = storage_path($attachment->path);
        $this->assertFileExists($filePath);

        $details = [
            'name' => 'My updated API attachment',
            'link' => 'https://cats.example.com'
        ];

        $resp = $this->putJson("{$this->baseEndpoint}/{$attachment->id}", $details);
        $resp->assertStatus(200);
        $attachment->refresh();

        $this->assertFileDoesNotExist($filePath);
        $this->assertTrue($attachment->external);
        $this->assertEquals('https://cats.example.com', $attachment->path);
        $this->assertEquals('', $attachment->extension);
    }

    public function test_delete_endpoint()
    {
        $this->actingAsApiAdmin();
        /** @var Page $page */
        $page = Page::query()->first();
        $attachment = $this->createAttachmentForPage($page);

        $resp = $this->deleteJson("{$this->baseEndpoint}/{$attachment->id}");

        $resp->assertStatus(204);
        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
    }

    protected function createAttachmentForPage(Page $page, $attributes = []): Attachment
    {
        $admin = $this->getAdmin();
        /** @var Attachment $attachment */
        $attachment = $page->attachments()->forceCreate(array_merge([
            'uploaded_to' => $page->id,
            'name' => 'test attachment',
            'external' => true,
            'order' => 1,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
            'path' => 'https://attachment.example.com'
        ], $attributes));
        return $attachment;
    }

    /**
     * Get a test file that can be uploaded.
     */
    protected function getTestFile(string $fileName): UploadedFile
    {
        return new UploadedFile(base_path('tests/test-data/test-file.txt'), $fileName, 'text/plain', 55, null, true);
    }
}
