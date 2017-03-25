<?php namespace Tests;

class ImageTest extends BrowserKitTest
{

    /**
     * Get a test image that can be uploaded
     * @param $fileName
     * @return \Illuminate\Http\UploadedFile
     */
    protected function getTestImage($fileName)
    {
        return new \Illuminate\Http\UploadedFile(base_path('tests/test-data/test-image.jpg'), $fileName, 'image/jpeg', 5238);
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
     * @return string
     */
    protected function uploadImage($name, $uploadedTo = 0)
    {
        $file = $this->getTestImage($name);
        $this->call('POST', '/images/gallery/upload', ['uploaded_to' => $uploadedTo], [], ['file' => $file], []);
        return $this->getTestImagePath('gallery', $name);
    }

    /**
     * Delete an uploaded image.
     * @param $relPath
     */
    protected function deleteImage($relPath)
    {
        unlink(public_path($relPath));
    }


    public function test_image_upload()
    {
        $page = \BookStack\Page::first();
        $this->asAdmin();
        $admin = $this->getAdmin();
        $imageName = 'first-image.jpg';

        $relPath = $this->uploadImage($imageName, $page->id);
        $this->assertResponseOk();

        $this->assertTrue(file_exists(public_path($relPath)), 'Uploaded image not found at path: '. public_path($relPath));

        $this->deleteImage($relPath);

        $this->seeInDatabase('images', [
            'url' => $this->baseUrl . $relPath,
            'type' => 'gallery',
            'uploaded_to' => $page->id,
            'path' => $relPath,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
            'name' => $imageName
        ]);

    }

    public function test_image_delete()
    {
        $page = \BookStack\Page::first();
        $this->asAdmin();
        $imageName = 'first-image.jpg';

        $relPath = $this->uploadImage($imageName, $page->id);
        $image = \BookStack\Image::first();

        $this->call('DELETE', '/images/' . $image->id);
        $this->assertResponseOk();

        $this->dontSeeInDatabase('images', [
            'url' => $this->baseUrl . $relPath,
            'type' => 'gallery'
        ]);

        $this->assertFalse(file_exists(public_path($relPath)), 'Uploaded image has not been deleted as expected');
    }

}