<?php

namespace BookStack\Uploads;

use BookStack\Util\FilePathNormalizer;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Log;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\Visibility;
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
        $trimmed = str_replace('uploads/images/', '', $path);
        $normalized = FilePathNormalizer::normalize($trimmed);

        if ($this->usingSecureImages()) {
            return $normalized;
        }

        return 'uploads/images/' . $normalized;
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
     * Get a stream to the file at the given path.
     * @returns ?resource
     */
    public function stream(string $path): mixed
    {
        return $this->filesystem->readStream($this->adjustPathForDisk($path));
    }

    /**
     * Save the given image data at the given path. Can choose to set
     * the image as public which will update its visibility after saving.
     */
    public function put(string $path, string $data, bool $makePublic = false): void
    {
        $path = $this->adjustPathForDisk($path);
        $this->filesystem->put($path, $data);

        // Set public visibility to ensure public access on S3, or that the file is accessible
        // to other processes (like web-servers) for local file storage options.
        // We avoid attempting this for (non-AWS) s3-like systems (even in a try-catch) as
        // we've always avoided setting permissions for s3-like due to potential issues,
        // with docs advising setting pre-configured permissions instead.
        // We also don't do this as the default filesystem/driver level as that can technically
        // require different ACLs for S3, and this provides us more logical control.
        if ($makePublic && !$this->isS3Like()) {
            try {
                $this->filesystem->setVisibility($path, Visibility::PUBLIC);
            } catch (UnableToSetVisibility $e) {
                Log::warning("Unable to set visibility for image upload with relative path: {$path}");
            }
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
