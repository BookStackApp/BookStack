<?php namespace Tests\Uploads;


use Illuminate\Http\UploadedFile;

trait UsesImages
{
    /**
     * Get the path to our basic test image.
     * @return string
     */
    protected function getTestImageFilePath()
    {
        return base_path('tests/test-data/test-image.png');
    }

    /**
     * Get a test image that can be uploaded
     * @param $fileName
     * @return UploadedFile
     */
    protected function getTestImage($fileName)
    {
        return new UploadedFile($this->getTestImageFilePath(), $fileName, 'image/png', 5238, null, true);
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
     * @param $type
     * @param $fileName
     * @return string
     */
    protected function getTestImagePath($type, $fileName)
    {
        return '/uploads/images/' . $type . '/' . Date('Y-m-M') . '/' . $fileName;
    }

    /**
     * Uploads an image with the given name.
     * @param $name
     * @param int $uploadedTo
     * @param string $contentType
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function uploadImage($name, $uploadedTo = 0, $contentType = 'image/png')
    {
        $file = $this->getTestImage($name);
        return $this->withHeader('Content-Type', $contentType)
            ->call('POST', '/images/gallery/upload', ['uploaded_to' => $uploadedTo], [], ['file' => $file], []);
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