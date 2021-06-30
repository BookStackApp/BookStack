<?php

namespace BookStack\Uploads;

use BookStack\Exceptions\FileUploadException;
use Exception;
use Illuminate\Contracts\Filesystem\Factory as FileSystem;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem as FileSystemInstance;
use Illuminate\Support\Str;
use Log;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AttachmentService
{
    protected $fileSystem;

    /**
     * AttachmentService constructor.
     */
    public function __construct(FileSystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * Get the storage that will be used for storing files.
     */
    protected function getStorage(): FileSystemInstance
    {
        $storageType = config('filesystems.attachments');

        // Override default location if set to local public to ensure not visible.
        if ($storageType === 'local') {
            $storageType = 'local_secure';
        }

        return $this->fileSystem->disk($storageType);
    }

    /**
     * Get an attachment from storage.
     *
     * @throws FileNotFoundException
     */
    public function getAttachmentFromStorage(Attachment $attachment): string
    {
        return $this->getStorage()->get($attachment->path);
    }

    /**
     * Store a new attachment upon user upload.
     *
     * @param UploadedFile $uploadedFile
     * @param int          $page_id
     *
     * @throws FileUploadException
     *
     * @return Attachment
     */
    public function saveNewUpload(UploadedFile $uploadedFile, $page_id)
    {
        $attachmentName = $uploadedFile->getClientOriginalName();
        $attachmentPath = $this->putFileInStorage($uploadedFile);
        $largestExistingOrder = Attachment::where('uploaded_to', '=', $page_id)->max('order');

        $attachment = Attachment::forceCreate([
            'name'        => $attachmentName,
            'path'        => $attachmentPath,
            'extension'   => $uploadedFile->getClientOriginalExtension(),
            'uploaded_to' => $page_id,
            'created_by'  => user()->id,
            'updated_by'  => user()->id,
            'order'       => $largestExistingOrder + 1,
        ]);

        return $attachment;
    }

    /**
     * Store a upload, saving to a file and deleting any existing uploads
     * attached to that file.
     *
     * @param UploadedFile $uploadedFile
     * @param Attachment   $attachment
     *
     * @throws FileUploadException
     *
     * @return Attachment
     */
    public function saveUpdatedUpload(UploadedFile $uploadedFile, Attachment $attachment)
    {
        if (!$attachment->external) {
            $this->deleteFileInStorage($attachment);
        }

        $attachmentName = $uploadedFile->getClientOriginalName();
        $attachmentPath = $this->putFileInStorage($uploadedFile);

        $attachment->name = $attachmentName;
        $attachment->path = $attachmentPath;
        $attachment->external = false;
        $attachment->extension = $uploadedFile->getClientOriginalExtension();
        $attachment->save();

        return $attachment;
    }

    /**
     * Save a new File attachment from a given link and name.
     */
    public function saveNewFromLink(string $name, string $link, int $page_id): Attachment
    {
        $largestExistingOrder = Attachment::where('uploaded_to', '=', $page_id)->max('order');

        return Attachment::forceCreate([
            'name'        => $name,
            'path'        => $link,
            'external'    => true,
            'extension'   => '',
            'uploaded_to' => $page_id,
            'created_by'  => user()->id,
            'updated_by'  => user()->id,
            'order'       => $largestExistingOrder + 1,
        ]);
    }

    /**
     * Updates the ordering for a listing of attached files.
     */
    public function updateFileOrderWithinPage(array $attachmentOrder, string $pageId)
    {
        foreach ($attachmentOrder as $index => $attachmentId) {
            Attachment::query()->where('uploaded_to', '=', $pageId)
                ->where('id', '=', $attachmentId)
                ->update(['order' => $index]);
        }
    }

    /**
     * Update the details of a file.
     */
    public function updateFile(Attachment $attachment, array $requestData): Attachment
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
     *
     * @param Attachment $attachment
     *
     * @throws Exception
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
     *
     * @param Attachment $attachment
     */
    protected function deleteFileInStorage(Attachment $attachment)
    {
        $storage = $this->getStorage();
        $dirPath = dirname($attachment->path);

        $storage->delete($attachment->path);
        if (count($storage->allFiles($dirPath)) === 0) {
            $storage->deleteDirectory($dirPath);
        }
    }

    /**
     * Store a file in storage with the given filename.
     *
     * @param UploadedFile $uploadedFile
     *
     * @throws FileUploadException
     *
     * @return string
     */
    protected function putFileInStorage(UploadedFile $uploadedFile)
    {
        $attachmentData = file_get_contents($uploadedFile->getRealPath());

        $storage = $this->getStorage();
        $basePath = 'uploads/files/' . date('Y-m-M') . '/';

        $uploadFileName = Str::random(16) . '.' . $uploadedFile->getClientOriginalExtension();
        while ($storage->exists($basePath . $uploadFileName)) {
            $uploadFileName = Str::random(3) . $uploadFileName;
        }

        $attachmentPath = $basePath . $uploadFileName;

        try {
            $storage->put($attachmentPath, $attachmentData);
        } catch (Exception $e) {
            Log::error('Error when attempting file upload:' . $e->getMessage());

            throw new FileUploadException(trans('errors.path_not_writable', ['filePath' => $attachmentPath]));
        }

        return $attachmentPath;
    }
}
