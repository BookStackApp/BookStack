<?php namespace Tests\Uploads;

use BookStack\Entities\Models\Page;
use Illuminate\Http\UploadedFile;

trait UsesImages
{
    /**
     * Get the path to our basic test image.
     * @return string
     */
    protected function getTestImageFilePath(?string $fileName = null)
    {
        if (is_null($fileName)) {
            $fileName = 'test-image.png';
        }

        return base_path('tests/test-data/' . $fileName);
    }

    /**
     * Get a test image that can be uploaded
     * @param $fileName
     * @return UploadedFile
     */
    protected function getTestImage($fileName, ?string $testDataFileName = null)
    {
        return new UploadedFile($this->getTestImageFilePath($testDataFileName), $fileName, 'image/png', 5238, null, true);
    }

    /**
     * Get the raw file data for the test image.
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
        return '/uploads/images/' . $type . '/' . Date('Y-m') . '/' . $fileName;
    }

    /**
     * Uploads an image with the given name.
     * @param $name
     * @param int $uploadedTo
     * @param string $contentType
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
     * @param Page|null $page
     * @return array
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
            'name' => $imageName,
            'path' => $relPath,
            'page' => $page,
            'response' => json_decode($upload->getContent()),
        ];
    }

    /**
     * Delete an uploaded image.
     * @param $relPath
     */
    protected function deleteImage($relPath)
    {
        $path = public_path($relPath);
        if (file_exists($path)) {
            unlink($path);
        }
    }

}