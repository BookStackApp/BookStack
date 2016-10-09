<?php namespace BookStack\Services;


use BookStack\Exceptions\FileUploadException;
use BookStack\File;
use Exception;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService extends UploadService
{

    /**
     * Store a new file upon user upload.
     * @param UploadedFile $uploadedFile
     * @param int $page_id
     * @return File
     * @throws FileUploadException
     */
    public function saveNewUpload(UploadedFile $uploadedFile, $page_id)
    {
        $fileName = $uploadedFile->getClientOriginalName();
        $fileData = file_get_contents($uploadedFile->getRealPath());

        $storage = $this->getStorage();
        $fileBasePath = 'uploads/files/' . Date('Y-m-M') . '/';
        $storageBasePath = $this->getStorageBasePath() . $fileBasePath;

        $uploadFileName = $fileName;
        while ($storage->exists($storageBasePath . $uploadFileName)) {
            $uploadFileName = str_random(3) . $uploadFileName;
        }

        $filePath = $fileBasePath . $uploadFileName;
        $fileStoragePath = $this->getStorageBasePath() . $filePath;

        try {
            $storage->put($fileStoragePath, $fileData);
        } catch (Exception $e) {
            throw new FileUploadException('File path ' . $fileStoragePath . ' could not be uploaded to. Ensure it is writable to the server.');
        }

        $largestExistingOrder = File::where('uploaded_to', '=', $page_id)->max('order');

        $file = File::forceCreate([
            'name' => $fileName,
            'path' => $filePath,
            'uploaded_to' => $page_id,
            'created_by' => user()->id,
            'updated_by' => user()->id,
            'order' => $largestExistingOrder + 1
        ]);

        return $file;
    }

    /**
     * Get the file storage base path, amended for storage type.
     * This allows us to keep a generic path in the database.
     * @return string
     */
    private function getStorageBasePath()
    {
        return $this->isLocal() ? 'storage/' : '';
    }

    /**
     * Updates the file ordering for a listing of attached files.
     * @param array $fileList
     * @param $pageId
     */
    public function updateFileOrderWithinPage($fileList, $pageId)
    {
        foreach ($fileList as $index => $file) {
            File::where('uploaded_to', '=', $pageId)->where('id', '=', $file['id'])->update(['order' => $index]);
        }
    }

}