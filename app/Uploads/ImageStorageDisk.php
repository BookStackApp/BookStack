<?php

namespace BookStack\Uploads;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\WhitespacePathNormalizer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImageStorageDisk
{
    public function __construct(
        protected string $diskName,
        protected Filesystem $filesystem,
    ) {
    }

    /**
     * Check if local secure image storage (Fetched behind authentication)
     * is currently active in the instance.
     */
    public function usingSecureImages(): bool
    {
        return $this->diskName === 'local_secure_images';
    }

    /**
     * Change the originally provided path to fit any disk-specific requirements.
     * This also ensures the path is kept to the expected root folders.
     */
    protected function adjustPathForDisk(string $path): string
    {
        $path = (new WhitespacePathNormalizer())->normalizePath(str_replace('uploads/images/', '', $path));

        if ($this->usingSecureImages()) {
            return $path;
        }

        return 'uploads/images/' . $path;
    }

    /**
     * Check if a file at the given path exists.
     */
    public function exists(string $path): bool
    {
        return $this->filesystem->exists($this->adjustPathForDisk($path));
    }

    /**
     * Get the file at the given path.
     */
    public function get(string $path): ?string
    {
        return $this->filesystem->get($this->adjustPathForDisk($path));
    }

    /**
     * Save the given image data at the given path. Can choose to set
     * the image as public which will update its visibility after saving.
     */
    public function put(string $path, string $data, bool $makePublic = false): void
    {
        $path = $this->adjustPathForDisk($path);
        $this->filesystem->put($path, $data);

        // Set visibility when a non-AWS-s3, s3-like storage option is in use.
        // Done since this call can break s3-like services but desired for other image stores.
        // Attempting to set ACL during above put request requires different permissions
        // hence would technically be a breaking change for actual s3 usage.
        if ($makePublic && !$this->isS3Like()) {
            $this->filesystem->setVisibility($path, 'public');
        }
    }

    /**
     * Destroys an image at the given path.
     * Searches for image thumbnails in addition to main provided path.
     */
    public function destroyAllMatchingNameFromPath(string $path): void
    {
        $path = $this->adjustPathForDisk($path);

        $imageFolder = dirname($path);
        $imageFileName = basename($path);
        $allImages = collect($this->filesystem->allFiles($imageFolder));

        // Delete image files
        $imagesToDelete = $allImages->filter(function ($imagePath) use ($imageFileName) {
            return basename($imagePath) === $imageFileName;
        });
        $this->filesystem->delete($imagesToDelete->all());

        // Cleanup of empty folders
        $foldersInvolved = array_merge([$imageFolder], $this->filesystem->directories($imageFolder));
        foreach ($foldersInvolved as $directory) {
            if ($this->isFolderEmpty($directory)) {
                $this->filesystem->deleteDirectory($directory);
            }
        }
    }

    /**
     * Get the mime type of the file at the given path.
     * Only works for local filesystem adapters.
     */
    public function mimeType(string $path): string
    {
        $path = $this->adjustPathForDisk($path);
        return $this->filesystem instanceof FilesystemAdapter ? $this->filesystem->mimeType($path) : '';
    }

    /**
     * Get a stream response for the image at the given path.
     */
    public function response(string $path): StreamedResponse
    {
        return $this->filesystem->response($this->adjustPathForDisk($path));
    }

    /**
     * Check if the image storage in use is an S3-like (but not likely S3) external system.
     */
    protected function isS3Like(): bool
    {
        $usingS3 = $this->diskName === 's3';
        return $usingS3 && !is_null(config('filesystems.disks.s3.endpoint'));
    }

    /**
     * Check whether a folder is empty.
     */
    protected function isFolderEmpty(string $path): bool
    {
        $files = $this->filesystem->files($path);
        $folders = $this->filesystem->directories($path);

        return count($files) === 0 && count($folders) === 0;
    }
}
