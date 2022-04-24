<?php

namespace BookStack\Uploads;

use BookStack\Exceptions\FileUploadException;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem as Storage;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use League\Flysystem\Util;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AttachmentService
{
    protected FilesystemManager $fileSystem;

    /**
     * AttachmentService constructor.
     */
    public function __construct(FilesystemManager $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * Get the storage that will be used for storing files.
     */
    protected function getStorageDisk(): Storage
    {
        return $this->fileSystem->disk($this->getStorageDiskName());
    }

    /**
     * Get the name of the storage disk to use.
     */
    protected function getStorageDiskName(): string
    {
        $storageType = config('filesystems.attachments');

        // Change to our secure-attachment disk if any of the local options
        // are used to prevent escaping that location.
        if ($storageType === 'local' || $storageType === 'local_secure') {
            $storageType = 'local_secure_attachments';
        }

        return $storageType;
    }

    /**
     * Change the originally provided path to fit any disk-specific requirements.
     * This also ensures the path is kept to the expected root folders.
     */
    protected function adjustPathForStorageDisk(string $path): string
    {
        $path = Util::normalizePath(str_replace('uploads/files/', '', $path));

        if ($this->getStorageDiskName() === 'local_secure_attachments') {
            return $path;
        }

        return 'uploads/files/' . $path;
    }

    /**
     * Get an attachment from storage.
     *
     * @throws FileNotFoundException
     */
    public function getAttachmentFromStorage(Attachment $attachment): string
    {
        return $this->getStorageDisk()->get($this->adjustPathForStorageDisk($attachment->path));
    }

    /**
     * Stream an attachment from storage.
     *
     * @throws FileNotFoundException
     *
     * @return resource|null
     */
    public function streamAttachmentFromStorage(Attachment $attachment)
    {
        return $this->getStorageDisk()->readStream($this->adjustPathForStorageDisk($attachment->path));
    }

    /**
     * Store a new attachment upon user upload.
     *
     * @throws FileUploadException
     */
    public function saveNewUpload(UploadedFile $uploadedFile, int $pageId): Attachment
    {
        $attachmentName = $uploadedFile->getClientOriginalName();
        $attachmentPath = $this->putFileInStorage($uploadedFile);
        $largestExistingOrder = Attachment::query()->where('uploaded_to', '=', $pageId)->max('order');

        /** @var Attachment $attachment */
        $attachment = Attachment::query()->forceCreate([
            'name'        => $attachmentName,
            'path'        => $attachmentPath,
            'extension'   => $uploadedFile->getClientOriginalExtension(),
            'uploaded_to' => $pageId,
            'created_by'  => user()->id,
            'updated_by'  => user()->id,
            'order'       => $largestExistingOrder + 1,
        ]);

        return $attachment;
    }

    /**
     * Store an upload, saving to a file and deleting any existing uploads
     * attached to that file.
     *
     * @throws FileUploadException
     */
    public function saveUpdatedUpload(UploadedFile $uploadedFile, Attachment $attachment): Attachment
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
        $link = trim($requestData['link'] ?? '');

        if (!empty($link)) {
            if (!$attachment->external) {
                $this->deleteFileInStorage($attachment);
                $attachment->external = true;
                $attachment->extension = '';
            }
            $attachment->path = $requestData['link'];
        }

        $attachment->save();

        return $attachment->refresh();
    }

    /**
     * Delete a File from the database and storage.
     *
     * @throws Exception
     */
    public function deleteFile(Attachment $attachment)
    {
        if (!$attachment->external) {
            $this->deleteFileInStorage($attachment);
        }

        $attachment->delete();
    }

    /**
     * Delete a file from the filesystem it sits on.
     * Cleans any empty leftover folders.
     */
    protected function deleteFileInStorage(Attachment $attachment)
    {
        $storage = $this->getStorageDisk();
        $dirPath = $this->adjustPathForStorageDisk(dirname($attachment->path));

        $storage->delete($this->adjustPathForStorageDisk($attachment->path));
        if (count($storage->allFiles($dirPath)) === 0) {
            $storage->deleteDirectory($dirPath);
        }
    }

    /**
     * Store a file in storage with the given filename.
     *
     * @throws FileUploadException
     */
    protected function putFileInStorage(UploadedFile $uploadedFile): string
    {
        $storage = $this->getStorageDisk();
        $basePath = 'uploads/files/' . date('Y-m-M') . '/';

        $uploadFileName = Str::random(16) . '-' . $uploadedFile->getClientOriginalExtension();
        while ($storage->exists($this->adjustPathForStorageDisk($basePath . $uploadFileName))) {
            $uploadFileName = Str::random(3) . $uploadFileName;
        }

        $attachmentStream = fopen($uploadedFile->getRealPath(), 'r');
        $attachmentPath = $basePath . $uploadFileName;

        try {
            $storage->writeStream($this->adjustPathForStorageDisk($attachmentPath), $attachmentStream);
        } catch (Exception $e) {
            Log::error('Error when attempting file upload:' . $e->getMessage());

            throw new FileUploadException(trans('errors.path_not_writable', ['filePath' => $attachmentPath]));
        }

        return $attachmentPath;
    }

    /**
     * Get the file validation rules for attachments.
     */
    public function getFileValidationRules(): array
    {
        return ['file', 'max:' . (config('app.upload_limit') * 1000)];
    }
}
