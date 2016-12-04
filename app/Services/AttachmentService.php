<?php namespace BookStack\Services;

use BookStack\Exceptions\FileUploadException;
use BookStack\Attachment;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AttachmentService extends UploadService
{

    /**
     * Get an attachment from storage.
     * @param Attachment $attachment
     * @return string
     */
    public function getAttachmentFromStorage(Attachment $attachment)
    {
        $attachmentPath = $this->getStorageBasePath() . $attachment->path;
        return $this->getStorage()->get($attachmentPath);
    }

    /**
     * Store a new attachment upon user upload.
     * @param UploadedFile $uploadedFile
     * @param int $page_id
     * @return Attachment
     * @throws FileUploadException
     */
    public function saveNewUpload(UploadedFile $uploadedFile, $page_id)
    {
        $attachmentName = $uploadedFile->getClientOriginalName();
        $attachmentPath = $this->putFileInStorage($attachmentName, $uploadedFile);
        $largestExistingOrder = Attachment::where('uploaded_to', '=', $page_id)->max('order');

        $attachment = Attachment::forceCreate([
            'name' => $attachmentName,
            'path' => $attachmentPath,
            'extension' => $uploadedFile->getClientOriginalExtension(),
            'uploaded_to' => $page_id,
            'created_by' => user()->id,
            'updated_by' => user()->id,
            'order' => $largestExistingOrder + 1
        ]);

        return $attachment;
    }

    /**
     * Store a upload, saving to a file and deleting any existing uploads
     * attached to that file.
     * @param UploadedFile $uploadedFile
     * @param Attachment $attachment
     * @return Attachment
     * @throws FileUploadException
     */
    public function saveUpdatedUpload(UploadedFile $uploadedFile, Attachment $attachment)
    {
        if (!$attachment->external) {
            $this->deleteFileInStorage($attachment);
        }

        $attachmentName = $uploadedFile->getClientOriginalName();
        $attachmentPath = $this->putFileInStorage($attachmentName, $uploadedFile);

        $attachment->name = $attachmentName;
        $attachment->path = $attachmentPath;
        $attachment->external = false;
        $attachment->extension = $uploadedFile->getClientOriginalExtension();
        $attachment->save();
        return $attachment;
    }

    /**
     * Save a new File attachment from a given link and name.
     * @param string $name
     * @param string $link
     * @param int $page_id
     * @return Attachment
     */
    public function saveNewFromLink($name, $link, $page_id)
    {
        $largestExistingOrder = Attachment::where('uploaded_to', '=', $page_id)->max('order');
        return Attachment::forceCreate([
            'name' => $name,
            'path' => $link,
            'external' => true,
            'extension' => '',
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
     * @param array $attachmentList
     * @param $pageId
     */
    public function updateFileOrderWithinPage($attachmentList, $pageId)
    {
        foreach ($attachmentList as $index => $attachment) {
            Attachment::where('uploaded_to', '=', $pageId)->where('id', '=', $attachment['id'])->update(['order' => $index]);
        }
    }


    /**
     * Update the details of a file.
     * @param Attachment $attachment
     * @param $requestData
     * @return Attachment
     */
    public function updateFile(Attachment $attachment, $requestData)
    {
        $attachment->name = $requestData['name'];
        if (isset($requestData['link']) && trim($requestData['link']) !== '') {
            $attachment->path = $requestData['link'];
            if (!$attachment->external) {
                $this->deleteFileInStorage($attachment);
                $attachment->external = true;
            }
        }
        $attachment->save();
        return $attachment;
    }

    /**
     * Delete a File from the database and storage.
     * @param Attachment $attachment
     */
    public function deleteFile(Attachment $attachment)
    {
        if ($attachment->external) {
            $attachment->delete();
            return;
        }
        
        $this->deleteFileInStorage($attachment);
        $attachment->delete();
    }

    /**
     * Delete a file from the filesystem it sits on.
     * Cleans any empty leftover folders.
     * @param Attachment $attachment
     */
    protected function deleteFileInStorage(Attachment $attachment)
    {
        $storedFilePath = $this->getStorageBasePath() . $attachment->path;
        $storage = $this->getStorage();
        $dirPath = dirname($storedFilePath);

        $storage->delete($storedFilePath);
        if (count($storage->allFiles($dirPath)) === 0) {
            $storage->deleteDirectory($dirPath);
        }
    }

    /**
     * Store a file in storage with the given filename
     * @param $attachmentName
     * @param UploadedFile $uploadedFile
     * @return string
     * @throws FileUploadException
     */
    protected function putFileInStorage($attachmentName, UploadedFile $uploadedFile)
    {
        $attachmentData = file_get_contents($uploadedFile->getRealPath());

        $storage = $this->getStorage();
        $attachmentBasePath = 'uploads/files/' . Date('Y-m-M') . '/';
        $storageBasePath = $this->getStorageBasePath() . $attachmentBasePath;

        $uploadFileName = $attachmentName;
        while ($storage->exists($storageBasePath . $uploadFileName)) {
            $uploadFileName = str_random(3) . $uploadFileName;
        }

        $attachmentPath = $attachmentBasePath . $uploadFileName;
        $attachmentStoragePath = $this->getStorageBasePath() . $attachmentPath;

        try {
            $storage->put($attachmentStoragePath, $attachmentData);
        } catch (Exception $e) {
            throw new FileUploadException(trans('errors.path_not_writable', ['filePath' => $attachmentStoragePath]));
        }
        return $attachmentPath;
    }

}