<?php

namespace BookStack\Uploads;

use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Str;

class ImageStorage
{
    public function __construct(
        protected FilesystemManager $fileSystem,
    ) {
    }

    /**
     * Get the storage disk for the given image type.
     */
    public function getDisk(string $imageType = ''): ImageStorageDisk
    {
        $diskName = $this->getDiskName($imageType);

        return new ImageStorageDisk(
            $diskName,
            $this->fileSystem->disk($diskName),
        );
    }

    /**
     * Check if "local secure restricted" (Fetched behind auth, with permissions enforced)
     * is currently active in the instance.
     */
    public function usingSecureRestrictedImages(): bool
    {
        return config('filesystems.images') === 'local_secure_restricted';
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
        $storageType = strtolower(config('filesystems.images'));
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
}
