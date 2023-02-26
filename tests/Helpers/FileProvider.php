<?php

namespace Tests\Helpers;

use BookStack\Entities\Models\Page;
use BookStack\Uploads\Attachment;
use BookStack\Uploads\AttachmentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\TestResponse;
use stdClass;
use Tests\TestCase;

class FileProvider
{
    /**
     * Get the path to a file in the test-data-directory.
     */
    public function testFilePath(string $fileName): string
    {
        return base_path('tests/test-data/' . $fileName);
    }

    /**
     * Creates a new temporary image file using the given name,
     * with the content decoded from the given bas64 file name.
     * Is generally used for testing sketchy files that could trip AV.
     */
    public function imageFromBase64File(string $base64FileName, string $imageFileName): UploadedFile
    {
        $imagePath = implode(DIRECTORY_SEPARATOR, [sys_get_temp_dir(), $imageFileName]);
        $base64FilePath = $this->testFilePath($base64FileName);
        $data = file_get_contents($base64FilePath);
        $decoded = base64_decode($data);
        file_put_contents($imagePath, $decoded);

        return new UploadedFile($imagePath, $imageFileName, 'image/png', null, true);
    }

    /**
     * Get a test image UploadedFile instance, that can be uploaded via test requests.
     */
    public function uploadedImage(string $fileName, string $testDataFileName = ''): UploadedFile
    {
        return new UploadedFile($this->testFilePath($testDataFileName ?: 'test-image.png'), $fileName, 'image/png', null, true);
    }

    /**
     * Get a test txt UploadedFile instance, that can be uploaded via test requests.
     */
    public function uploadedTextFile(string $fileName): UploadedFile
    {
        return new UploadedFile($this->testFilePath('test-file.txt'), $fileName, 'text/plain', null, true);
    }

    /**
     * Get raw data for a PNG image test file.
     */
    public function pngImageData(): string
    {
        return file_get_contents($this->testFilePath('test-image.png'));
    }

    /**
     * Get the expected relative path for an uploaded image of the given type and filename.
     */
    public function expectedImagePath(string $imageType, string $fileName): string
    {
        return '/uploads/images/' . $imageType . '/' . date('Y-m') . '/' . $fileName;
    }

    /**
     * Performs an image gallery upload request with the given name.
     */
    public function uploadGalleryImage(TestCase $case, string $name, int $uploadedTo = 0, string $contentType = 'image/png', string $testDataFileName = ''): TestResponse
    {
        $file = $this->uploadedImage($name, $testDataFileName);

        return $case->call('POST', '/images/gallery', ['uploaded_to' => $uploadedTo], [], ['file' => $file], ['CONTENT_TYPE' => $contentType]);
    }

    /**
     * Upload a new gallery image and return a set of details about the image,
     * including the json decoded response of the upload.
     * Ensures the upload succeeds.
     *
     * @return array{name: string, path: string, page: Page, response: stdClass}
     */
    public function uploadGalleryImageToPage(TestCase $case, Page $page, string $testDataFileName = ''): array
    {
        $imageName = $testDataFileName ?: 'first-image.png';
        $relPath = $this->expectedImagePath('gallery', $imageName);
        $this->deleteAtRelativePath($relPath);

        $upload = $this->uploadGalleryImage($case, $imageName, $page->id, 'image/png', $testDataFileName);
        $upload->assertStatus(200);

        return [
            'name' => $imageName,
            'path' => $relPath,
            'page' => $page,
            'response' => json_decode($upload->getContent()),
        ];
    }

    /**
     * Uploads an attachment file with the given name.
     */
    public function uploadAttachmentFile(TestCase $case, string $name, int $uploadedTo = 0): TestResponse
    {
        $file = $this->uploadedTextFile($name);

        return $case->call('POST', '/attachments/upload', ['uploaded_to' => $uploadedTo], [], ['file' => $file], []);
    }

    /**
     * Upload a new attachment from the given raw data of the given type, to the given page.
     * Returns the attachment
     */
    public function uploadAttachmentDataToPage(TestCase $case, Page $page, string $filename, string $content, string $mimeType): Attachment
    {
        $file = tmpfile();
        $filePath = stream_get_meta_data($file)['uri'];
        file_put_contents($filePath, $content);
        $upload = new UploadedFile($filePath, $filename, $mimeType, null, true);

        $case->call('POST', '/attachments/upload', ['uploaded_to' => $page->id], [], ['file' => $upload], []);

        return $page->attachments()->where('uploaded_to', '=', $page->id)->latest()->firstOrFail();
    }

    /**
     * Delete an uploaded image.
     */
    public function deleteAtRelativePath(string $path): void
    {
        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    /**
     * Delete all uploaded files.
     * To assist with cleanup.
     */
    public function deleteAllAttachmentFiles(): void
    {
        $fileService = app()->make(AttachmentService::class);
        foreach (Attachment::all() as $file) {
            $fileService->deleteFile($file);
        }
    }
}
