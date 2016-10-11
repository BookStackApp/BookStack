<?php namespace BookStack\Services;


use BookStack\Exceptions\FileUploadException;
use BookStack\File;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService extends UploadService
{

    /**
     * Get a file from storage.
     * @param File $file
     * @return string
     */
    public function getFile(File $file)
    {
        $filePath = $this->getStorageBasePath() . $file->path;
        return $this->getStorage()->get($filePath);
    }

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
        $filePath = $this->putFileInStorage($fileName, $uploadedFile);
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
     * Store a upload, saving to a file and deleting any existing uploads
     * attached to that file.
     * @param UploadedFile $uploadedFile
     * @param File $file
     * @return File
     * @throws FileUploadException
     */
    public function saveUpdatedUpload(UploadedFile $uploadedFile, File $file)
    {
        if (!$file->external) {
            $this->deleteFileInStorage($file);
        }

        $fileName = $uploadedFile->getClientOriginalName();
        $filePath = $this->putFileInStorage($fileName, $uploadedFile);

        $file->name = $fileName;
        $file->path = $filePath;
        $file->external = false;
        $file->save();
        return $file;
    }

    /**
     * Save a new File attachment from a given link and name.
     * @param string $name
     * @param string $link
     * @param int $page_id
     * @return File
     */
    public function saveNewFromLink($name, $link, $page_id)
    {
        $largestExistingOrder = File::where('uploaded_to', '=', $page_id)->max('order');
        return File::forceCreate([
            'name' => $name,
            'path' => $link,
            'external' => true,
            'uploaded_to' => $page_id,
            'created_by' => user()->id,
            'updated_by' => user()->id,
            'order' => $largestExistingOrder + 1
        ]);
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


    /**
     * Update the details of a file.
     * @param File $file
     * @param $requestData
     * @return File
     */
    public function updateFile(File $file, $requestData)
    {
        $file->name = $requestData['name'];
        if (isset($requestData['link']) && trim($requestData['link']) !== '') {
            $file->path = $requestData['link'];
            if (!$file->external) {
                $this->deleteFileInStorage($file);
                $file->external = true;
            }
        }
        $file->save();
        return $file;
    }

    /**
     * Delete a File from the database and storage.
     * @param File $file
     */
    public function deleteFile(File $file)
    {
        if ($file->external) {
            $file->delete();
            return;
        }
        
        $this->deleteFileInStorage($file);
        $file->delete();
    }

    /**
     * Delete a file from the filesystem it sits on.
     * Cleans any empty leftover folders.
     * @param File $file
     */
    protected function deleteFileInStorage(File $file)
    {
        $storedFilePath = $this->getStorageBasePath() . $file->path;
        $storage = $this->getStorage();
        $dirPath = dirname($storedFilePath);

        $storage->delete($storedFilePath);
        if (count($storage->allFiles($dirPath)) === 0) {
            $storage->deleteDirectory($dirPath);
        }
    }

    /**
     * Store a file in storage with the given filename
     * @param $fileName
     * @param UploadedFile $uploadedFile
     * @return string
     * @throws FileUploadException
     */
    protected function putFileInStorage($fileName, UploadedFile $uploadedFile)
    {
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
        return $filePath;
    }

}