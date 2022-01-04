<?php

namespace Tests\Uploads;

use BookStack\Entities\Models\Page;
use Illuminate\Http\UploadedFile;
use stdClass;

trait UsesImages
{
    /**
     * Get the path to a file in the test-data-directory.
     */
    protected function getTestImageFilePath(?string $fileName = null): string
    {
        if (is_null($fileName)) {
            $fileName = 'test-image.png';
        }

        return base_path('tests/test-data/' . $fileName);
    }

    /**
     * Creates a new temporary image file using the given name,
     * with the content decoded from the given bas64 file name.
     * Is generally used for testing sketchy files that could trip AV.
     */
    protected function newTestImageFromBase64(string $base64FileName, $imageFileName): UploadedFile
    {
        $imagePath = implode(DIRECTORY_SEPARATOR, [sys_get_temp_dir(), $imageFileName]);
        $base64FilePath = $this->getTestImageFilePath($base64FileName);
        $data = file_get_contents($base64FilePath);
        $decoded = base64_decode($data);
        file_put_contents($imagePath, $decoded);

        return new UploadedFile($imagePath, $imageFileName, 'image/png', null, true);
    }

    /**
     * Get a test image that can be uploaded.
     */
    protected function getTestImage(string $fileName, ?string $testDataFileName = null): UploadedFile
    {
        return new UploadedFile($this->getTestImageFilePath($testDataFileName), $fileName, 'image/png', null, true);
    }

    /**
     * Get the raw file data for the test image.
     *
     * @return false|string
     */
    protected function getTestImageContent()
    {
        return file_get_contents($this->getTestImageFilePath());
    }

    /**
     * Get the path for a test image.
     */
    protected function getTestImagePath(string $type, string $fileName): string
    {
        return '/uploads/images/' . $type . '/' . date('Y-m') . '/' . $fileName;
    }

    /**
     * Uploads an image with the given name.
     *
     * @param $name
     * @param int    $uploadedTo
     * @param string $contentType
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function uploadImage($name, $uploadedTo = 0, $contentType = 'image/png', ?string $testDataFileName = null)
    {
        $file = $this->getTestImage($name, $testDataFileName);

        return $this->withHeader('Content-Type', $contentType)
            ->call('POST', '/images/gallery', ['uploaded_to' => $uploadedTo], [], ['file' => $file], []);
    }

    /**
     * Upload a new gallery image.
     * Returns the image name.
     * Can provide a page to relate the image to.
     *
     * @param Page|null $page
     *
     * @return array{name: string, path: string, page: Page, response: stdClass}
     */
    protected function uploadGalleryImage(Page $page = null, ?string $testDataFileName = null)
    {
        if ($page === null) {
            $page = Page::query()->first();
        }

        $imageName = $testDataFileName ?? 'first-image.png';
        $relPath = $this->getTestImagePath('gallery', $imageName);
        $this->deleteImage($relPath);

        $upload = $this->uploadImage($imageName, $page->id, 'image/png', $testDataFileName);
        $upload->assertStatus(200);

        return [
            'name'     => $imageName,
            'path'     => $relPath,
            'page'     => $page,
            'response' => json_decode($upload->getContent()),
        ];
    }

    /**
     * Delete an uploaded image.
     */
    protected function deleteImage(string $relPath)
    {
        $path = public_path($relPath);
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
