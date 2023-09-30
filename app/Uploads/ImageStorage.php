<?php

namespace BookStack\Uploads;

use Illuminate\Contracts\Filesystem\Filesystem as StorageDisk;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Str;
use League\Flysystem\WhitespacePathNormalizer;

class ImageStorage
{
    public function __construct(
        protected FilesystemManager $fileSystem,
    ) {
    }

    /**
     * Get the storage disk for the given image type.
     */
    public function getDisk(string $imageType = ''): StorageDisk
    {
        return $this->fileSystem->disk($this->getDiskName($imageType));
    }

    /**
     * Check if local secure image storage (Fetched behind authentication)
     * is currently active in the instance.
     */
    public function usingSecureImages(string $imageType = 'gallery'): bool
    {
        return $this->getDiskName($imageType) === 'local_secure_images';
    }

    /**
     * Check if "local secure restricted" (Fetched behind auth, with permissions enforced)
     * is currently active in the instance.
     */
    public function usingSecureRestrictedImages()
    {
        return config('filesystems.images') === 'local_secure_restricted';
    }

    /**
     * Change the originally provided path to fit any disk-specific requirements.
     * This also ensures the path is kept to the expected root folders.
     */
    public function adjustPathForDisk(string $path, string $imageType = ''): string
    {
        $path = (new WhitespacePathNormalizer())->normalizePath(str_replace('uploads/images/', '', $path));

        if ($this->usingSecureImages($imageType)) {
            return $path;
        }

        return 'uploads/images/' . $path;
    }

    /**
     * Clean up an image file name to be both URL and storage safe.
     */
    public function cleanImageFileName(string $name): string
    {
        $name = str_replace(' ', '-', $name);
        $nameParts = explode('.', $name);
        $extension = array_pop($nameParts);
        $name = implode('-', $nameParts);
        $name = Str::slug($name);

        if (strlen($name) === 0) {
            $name = Str::random(10);
        }

        return $name . '.' . $extension;
    }

    /**
     * Get the name of the storage disk to use.
     */
    protected function getDiskName(string $imageType): string
    {
        $storageType = config('filesystems.images');
        $localSecureInUse = ($storageType === 'local_secure' || $storageType === 'local_secure_restricted');

        // Ensure system images (App logo) are uploaded to a public space
        if ($imageType === 'system' && $localSecureInUse) {
            return 'local';
        }

        // Rename local_secure options to get our image specific storage driver which
        // is scoped to the relevant image directories.
        if ($localSecureInUse) {
            return 'local_secure_images';
        }

        return $storageType;
    }

    /**
     * Get a storage path for the given image URL.
     * Ensures the path will start with "uploads/images".
     * Returns null if the url cannot be resolved to a local URL.
     */
    public function urlToPath(string $url): ?string
    {
        $url = ltrim(trim($url), '/');

        // Handle potential relative paths
        $isRelative = !str_starts_with($url, 'http');
        if ($isRelative) {
            if (str_starts_with(strtolower($url), 'uploads/images')) {
                return trim($url, '/');
            }

            return null;
        }

        // Handle local images based on paths on the same domain
        $potentialHostPaths = [
            url('uploads/images/'),
            $this->getPublicUrl('/uploads/images/'),
        ];

        foreach ($potentialHostPaths as $potentialBasePath) {
            $potentialBasePath = strtolower($potentialBasePath);
            if (str_starts_with(strtolower($url), $potentialBasePath)) {
                return 'uploads/images/' . trim(substr($url, strlen($potentialBasePath)), '/');
            }
        }

        return null;
    }

    /**
     * Gets a public facing url for an image by checking relevant environment variables.
     * If s3-style store is in use it will default to guessing a public bucket URL.
     */
    public function getPublicUrl(string $filePath): string
    {
        $storageUrl = config('filesystems.url');

        // Get the standard public s3 url if s3 is set as storage type
        // Uses the nice, short URL if bucket name has no periods in otherwise the longer
        // region-based url will be used to prevent http issues.
        if (!$storageUrl && config('filesystems.images') === 's3') {
            $storageDetails = config('filesystems.disks.s3');
            if (!str_contains($storageDetails['bucket'], '.')) {
                $storageUrl = 'https://' . $storageDetails['bucket'] . '.s3.amazonaws.com';
            } else {
                $storageUrl = 'https://s3-' . $storageDetails['region'] . '.amazonaws.com/' . $storageDetails['bucket'];
            }
        }

        $basePath = $storageUrl ?: url('/');

        return rtrim($basePath, '/') . $filePath;
    }

    /**
     * Save image data for the given path in the public space, if possible,
     * for the provided storage mechanism.
     */
    public function storeInPublicSpace(StorageDisk $storage, string $path, string $data): void
    {
        $storage->put($path, $data);

        // Set visibility when a non-AWS-s3, s3-like storage option is in use.
        // Done since this call can break s3-like services but desired for other image stores.
        // Attempting to set ACL during above put request requires different permissions
        // hence would technically be a breaking change for actual s3 usage.
        if (!$this->isS3Like()) {
            $storage->setVisibility($path, 'public');
        }
    }

    /**
     * Check if the image storage in use is an S3-like (but not likely S3) external system.
     */
    protected function isS3Like(): bool
    {
        $usingS3 = strtolower(config('filesystems.images')) === 's3';
        return $usingS3 && !is_null(config('filesystems.disks.s3.endpoint'));
    }
}
