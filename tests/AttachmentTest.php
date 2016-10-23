<?php

class AttachmentTest extends TestCase
{
    /**
     * Get a test file that can be uploaded
     * @param $fileName
     * @return \Illuminate\Http\UploadedFile
     */
    protected function getTestFile($fileName)
    {
        return new \Illuminate\Http\UploadedFile(base_path('tests/test-data/test-file.txt'), $fileName, 'text/plain', 55, null, true);
    }

    /**
     * Uploads a file with the given name.
     * @param $name
     * @param int $uploadedTo
     * @return string
     */
    protected function uploadFile($name, $uploadedTo = 0)
    {
        $file = $this->getTestFile($name);
        return $this->call('POST', '/files/upload', ['uploaded_to' => $uploadedTo], [], ['file' => $file], []);
    }

    /**
     * Get the expected upload path for a file.
     * @param $fileName
     * @return string
     */
    protected function getUploadPath($fileName)
    {
        return 'uploads/files/' . Date('Y-m-M') . '/' . $fileName;
    }

    /**
     * Delete all uploaded files.
     * To assist with cleanup.
     */
    protected function deleteUploads()
    {
        $fileService = $this->app->make(\BookStack\Services\FileService::class);
        foreach (\BookStack\File::all() as $file) {
            $fileService->deleteFile($file);
        }
    }

    public function test_file_upload()
    {
        $page = \BookStack\Page::first();
        $this->asAdmin();
        $admin = $this->getAdmin();
        $fileName = 'upload_test_file.txt';

        $expectedResp = [
            'name' => $fileName,
            'uploaded_to'=> $page->id,
            'extension' => 'txt',
            'order' => 1,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
            'path' => $this->getUploadPath($fileName)
        ];

        $this->uploadFile($fileName, $page->id);
        $this->assertResponseOk();
        $this->seeJsonContains($expectedResp);
        $this->seeInDatabase('files', $expectedResp);

        $this->deleteUploads();
    }

    public function test_file_display_and_access()
    {
        $page = \BookStack\Page::first();
        $this->asAdmin();
        $admin = $this->getAdmin();
        $fileName = 'upload_test_file.txt';

        $this->uploadFile($fileName, $page->id);
        $this->assertResponseOk();
        $this->visit($page->getUrl())
            ->seeLink($fileName)
            ->click($fileName)
            ->see('Hi, This is a test file for testing the upload process.');

        $this->deleteUploads();
    }

    public function test_attaching_link_to_page()
    {
        $page = \BookStack\Page::first();
        $admin = $this->getAdmin();
        $this->asAdmin();

        $this->call('POST', 'files/link', [
            'link' => 'https://example.com',
            'name' => 'Example Attachment Link',
            'uploaded_to' => $page->id,
        ]);

        $expectedResp = [
            'path' => 'https://example.com',
            'name' => 'Example Attachment Link',
            'uploaded_to' => $page->id,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
            'external' => true,
            'order' => 1,
            'extension' => ''
        ];

        $this->assertResponseOk();
        $this->seeJsonContains($expectedResp);
        $this->seeInDatabase('files', $expectedResp);

        $this->visit($page->getUrl())->seeLink('Example Attachment Link')
            ->click('Example Attachment Link')->seePageIs('https://example.com');

        $this->deleteUploads();
    }

    public function test_attachment_updating()
    {
        $page = \BookStack\Page::first();
        $this->asAdmin();

        $this->call('POST', 'files/link', [
            'link' => 'https://example.com',
            'name' => 'Example Attachment Link',
            'uploaded_to' => $page->id,
        ]);

        $attachmentId = \BookStack\File::first()->id;

        $this->call('PUT', 'files/' . $attachmentId, [
            'uploaded_to' => $page->id,
            'name' => 'My new attachment name',
            'link' => 'https://test.example.com'
        ]);

        $expectedResp = [
            'path' => 'https://test.example.com',
            'name' => 'My new attachment name',
            'uploaded_to' => $page->id
        ];

        $this->assertResponseOk();
        $this->seeJsonContains($expectedResp);
        $this->seeInDatabase('files', $expectedResp);

        $this->deleteUploads();
    }

    public function test_file_deletion()
    {
        $page = \BookStack\Page::first();
        $this->asAdmin();
        $fileName = 'deletion_test.txt';
        $this->uploadFile($fileName, $page->id);

        $filePath = base_path('storage/' . $this->getUploadPath($fileName));

        $this->assertTrue(file_exists($filePath), 'File at path ' . $filePath . ' does not exist');

        $attachmentId = \BookStack\File::first()->id;
        $this->call('DELETE', 'files/' . $attachmentId);

        $this->dontSeeInDatabase('files', [
            'name' => $fileName
        ]);
        $this->assertFalse(file_exists($filePath), 'File at path ' . $filePath . ' was not deleted as expected');

        $this->deleteUploads();
    }

    public function test_attachment_deletion_on_page_deletion()
    {
        $page = \BookStack\Page::first();
        $this->asAdmin();
        $fileName = 'deletion_test.txt';
        $this->uploadFile($fileName, $page->id);

        $filePath = base_path('storage/' . $this->getUploadPath($fileName));

        $this->assertTrue(file_exists($filePath), 'File at path ' . $filePath . ' does not exist');
        $this->seeInDatabase('files', [
            'name' => $fileName
        ]);

        $this->call('DELETE', $page->getUrl());

        $this->dontSeeInDatabase('files', [
            'name' => $fileName
        ]);
        $this->assertFalse(file_exists($filePath), 'File at path ' . $filePath . ' was not deleted as expected');

        $this->deleteUploads();
    }
}
