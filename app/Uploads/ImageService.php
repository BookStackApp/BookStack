<?php

namespace BookStack\Uploads;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Page;
use BookStack\Exceptions\ImageUploadException;
use ErrorException;
use Exception;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem as Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Exception\NotSupportedException;
use Intervention\Image\Image as InterventionImage;
use Intervention\Image\ImageManager;
use League\Flysystem\Util;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImageService
{
    protected ImageManager $imageTool;
    protected Cache $cache;
    protected $storageUrl;
    protected FilesystemManager $fileSystem;

    protected static $supportedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function __construct(ImageManager $imageTool, FilesystemManager $fileSystem, Cache $cache)
    {
        $this->imageTool = $imageTool;
        $this->fileSystem = $fileSystem;
        $this->cache = $cache;
    }

    /**
     * Get the storage that will be used for storing images.
     */
    protected function getStorageDisk(string $imageType = ''): Storage
    {
        return $this->fileSystem->disk($this->getStorageDiskName($imageType));
    }

    /**
     * Check if local secure image storage (Fetched behind authentication)
     * is currently active in the instance.
     */
    protected function usingSecureImages(string $imageType = 'gallery'): bool
    {
        return $this->getStorageDiskName($imageType) === 'local_secure_images';
    }

    /**
     * Check if "local secure restricted" (Fetched behind auth, with permissions enforced)
     * is currently active in the instance.
     */
    protected function usingSecureRestrictedImages()
    {
        return config('filesystems.images') === 'local_secure_restricted';
    }

    /**
     * Change the originally provided path to fit any disk-specific requirements.
     * This also ensures the path is kept to the expected root folders.
     */
    protected function adjustPathForStorageDisk(string $path, string $imageType = ''): string
    {
        $path = Util::normalizePath(str_replace('uploads/images/', '', $path));

        if ($this->usingSecureImages($imageType)) {
            return $path;
        }

        return 'uploads/images/' . $path;
    }

    /**
     * Get the name of the storage disk to use.
     */
    protected function getStorageDiskName(string $imageType): string
    {
        $storageType = config('filesystems.images');

        // Ensure system images (App logo) are uploaded to a public space
        if ($imageType === 'system' && $storageType === 'local_secure') {
            $storageType = 'local';
        }

        // Rename local_secure options to get our image specific storage driver which
        // is scoped to the relevant image directories.
        if ($storageType === 'local_secure' || $storageType === 'local_secure_restricted') {
            $storageType = 'local_secure_images';
        }

        return $storageType;
    }

    /**
     * Saves a new image from an upload.
     *
     * @throws ImageUploadException
     *
     * @return mixed
     */
    public function saveNewFromUpload(
        UploadedFile $uploadedFile,
        string $type,
        int $uploadedTo = 0,
        int $resizeWidth = null,
        int $resizeHeight = null,
        bool $keepRatio = true
    ) {
        $imageName = $uploadedFile->getClientOriginalName();
        $imageData = file_get_contents($uploadedFile->getRealPath());

        if ($resizeWidth !== null || $resizeHeight !== null) {
            $imageData = $this->resizeImage($imageData, $resizeWidth, $resizeHeight, $keepRatio);
        }

        return $this->saveNew($imageName, $imageData, $type, $uploadedTo);
    }

    /**
     * Save a new image from a uri-encoded base64 string of data.
     *
     * @throws ImageUploadException
     */
    public function saveNewFromBase64Uri(string $base64Uri, string $name, string $type, int $uploadedTo = 0): Image
    {
        $splitData = explode(';base64,', $base64Uri);
        if (count($splitData) < 2) {
            throw new ImageUploadException('Invalid base64 image data provided');
        }
        $data = base64_decode($splitData[1]);

        return $this->saveNew($name, $data, $type, $uploadedTo);
    }

    /**
     * Save a new image into storage.
     *
     * @throws ImageUploadException
     */
    public function saveNew(string $imageName, string $imageData, string $type, int $uploadedTo = 0): Image
    {
        $storage = $this->getStorageDisk($type);
        $secureUploads = setting('app-secure-images');
        $fileName = $this->cleanImageFileName($imageName);

        $imagePath = '/uploads/images/' . $type . '/' . date('Y-m') . '/';

        while ($storage->exists($this->adjustPathForStorageDisk($imagePath . $fileName, $type))) {
            $fileName = Str::random(3) . $fileName;
        }

        $fullPath = $imagePath . $fileName;
        if ($secureUploads) {
            $fullPath = $imagePath . Str::random(16) . '-' . $fileName;
        }

        try {
            $this->saveImageDataInPublicSpace($storage, $this->adjustPathForStorageDisk($fullPath, $type), $imageData);
        } catch (Exception $e) {
            Log::error('Error when attempting image upload:' . $e->getMessage());

            throw new ImageUploadException(trans('errors.path_not_writable', ['filePath' => $fullPath]));
        }

        $imageDetails = [
            'name'        => $imageName,
            'path'        => $fullPath,
            'url'         => $this->getPublicUrl($fullPath),
            'type'        => $type,
            'uploaded_to' => $uploadedTo,
        ];

        if (user()->id !== 0) {
            $userId = user()->id;
            $imageDetails['created_by'] = $userId;
            $imageDetails['updated_by'] = $userId;
        }

        $image = (new Image())->forceFill($imageDetails);
        $image->save();

        return $image;
    }

    /**
     * Save image data for the given path in the public space, if possible,
     * for the provided storage mechanism.
     */
    protected function saveImageDataInPublicSpace(Storage $storage, string $path, string $data)
    {
        $storage->put($path, $data);

        // Set visibility when a non-AWS-s3, s3-like storage option is in use.
        // Done since this call can break s3-like services but desired for other image stores.
        // Attempting to set ACL during above put request requires different permissions
        // hence would technically be a breaking change for actual s3 usage.
        $usingS3 = strtolower(config('filesystems.images')) === 's3';
        $usingS3Like = $usingS3 && !is_null(config('filesystems.disks.s3.endpoint'));
        if (!$usingS3Like) {
            $storage->setVisibility($path, 'public');
        }
    }

    /**
     * Clean up an image file name to be both URL and storage safe.
     */
    protected function cleanImageFileName(string $name): string
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
     * Checks if the image is a gif. Returns true if it is, else false.
     */
    protected function isGif(Image $image): bool
    {
        return strtolower(pathinfo($image->path, PATHINFO_EXTENSION)) === 'gif';
    }

    /**
     * Check if the given image and image data is apng.
     */
    protected function isApngData(Image $image, string &$imageData): bool
    {
        $isPng = strtolower(pathinfo($image->path, PATHINFO_EXTENSION)) === 'png';
        if (!$isPng) {
            return false;
        }

        $initialHeader = substr($imageData, 0, strpos($imageData, 'IDAT'));

        return strpos($initialHeader, 'acTL') !== false;
    }

    /**
     * Get the thumbnail for an image.
     * If $keepRatio is true only the width will be used.
     * Checks the cache then storage to avoid creating / accessing the filesystem on every check.
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getThumbnail(Image $image, ?int $width, ?int $height, bool $keepRatio = false): string
    {
        // Do not resize GIF images where we're not cropping
        if ($keepRatio && $this->isGif($image)) {
            return $this->getPublicUrl($image->path);
        }

        $thumbDirName = '/' . ($keepRatio ? 'scaled-' : 'thumbs-') . $width . '-' . $height . '/';
        $imagePath = $image->path;
        $thumbFilePath = dirname($imagePath) . $thumbDirName . basename($imagePath);

        $thumbCacheKey = 'images::' . $image->id . '::' . $thumbFilePath;

        // Return path if in cache
        $cachedThumbPath = $this->cache->get($thumbCacheKey);
        if ($cachedThumbPath) {
            return $this->getPublicUrl($cachedThumbPath);
        }

        // If thumbnail has already been generated, serve that and cache path
        $storage = $this->getStorageDisk($image->type);
        if ($storage->exists($this->adjustPathForStorageDisk($thumbFilePath, $image->type))) {
            $this->cache->put($thumbCacheKey, $thumbFilePath, 60 * 60 * 72);

            return $this->getPublicUrl($thumbFilePath);
        }

        $imageData = $storage->get($this->adjustPathForStorageDisk($imagePath, $image->type));

        // Do not resize apng images where we're not cropping
        if ($keepRatio && $this->isApngData($image, $imageData)) {
            $this->cache->put($thumbCacheKey, $image->path, 60 * 60 * 72);

            return $this->getPublicUrl($image->path);
        }

        // If not in cache and thumbnail does not exist, generate thumb and cache path
        $thumbData = $this->resizeImage($imageData, $width, $height, $keepRatio);
        $this->saveImageDataInPublicSpace($storage, $this->adjustPathForStorageDisk($thumbFilePath, $image->type), $thumbData);
        $this->cache->put($thumbCacheKey, $thumbFilePath, 60 * 60 * 72);

        return $this->getPublicUrl($thumbFilePath);
    }

    /**
     * Resize the image of given data to the specified size, and return the new image data.
     *
     * @throws ImageUploadException
     */
    protected function resizeImage(string $imageData, ?int $width, ?int $height, bool $keepRatio): string
    {
        try {
            $thumb = $this->imageTool->make($imageData);
        } catch (ErrorException|NotSupportedException $e) {
            throw new ImageUploadException(trans('errors.cannot_create_thumbs'));
        }

        $this->orientImageToOriginalExif($thumb, $imageData);

        if ($keepRatio) {
            $thumb->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        } else {
            $thumb->fit($width, $height);
        }

        $thumbData = (string) $thumb->encode();

        // Use original image data if we're keeping the ratio
        // and the resizing does not save any space.
        if ($keepRatio && strlen($thumbData) > strlen($imageData)) {
            return $imageData;
        }

        return $thumbData;
    }

    /**
     * Orientate the given intervention image based upon the given original image data.
     * Intervention does have an `orientate` method but the exif data it needs is lost before it
     * can be used (At least when created using binary string data) so we need to do some
     * implementation on our side to use the original image data.
     * Bulk of logic taken from: https://github.com/Intervention/image/blob/b734a4988b2148e7d10364b0609978a88d277536/src/Intervention/Image/Commands/OrientateCommand.php
     * Copyright (c) Oliver Vogel, MIT License.
     */
    protected function orientImageToOriginalExif(InterventionImage $image, string $originalData): void
    {
        if (!extension_loaded('exif')) {
            return;
        }

        $stream = Utils::streamFor($originalData)->detach();
        $exif = @exif_read_data($stream);
        $orientation = $exif ? ($exif['Orientation'] ?? null) : null;

        switch ($orientation) {
            case 2:
                $image->flip();
                break;
            case 3:
                $image->rotate(180);
                break;
            case 4:
                $image->rotate(180)->flip();
                break;
            case 5:
                $image->rotate(270)->flip();
                break;
            case 6:
                $image->rotate(270);
                break;
            case 7:
                $image->rotate(90)->flip();
                break;
            case 8:
                $image->rotate(90);
                break;
        }
    }

    /**
     * Get the raw data content from an image.
     *
     * @throws FileNotFoundException
     */
    public function getImageData(Image $image): string
    {
        $storage = $this->getStorageDisk();

        return $storage->get($this->adjustPathForStorageDisk($image->path, $image->type));
    }

    /**
     * Destroy an image along with its revisions, thumbnails and remaining folders.
     *
     * @throws Exception
     */
    public function destroy(Image $image)
    {
        $this->destroyImagesFromPath($image->path, $image->type);
        $image->delete();
    }

    /**
     * Destroys an image at the given path.
     * Searches for image thumbnails in addition to main provided path.
     */
    protected function destroyImagesFromPath(string $path, string $imageType): bool
    {
        $path = $this->adjustPathForStorageDisk($path, $imageType);
        $storage = $this->getStorageDisk($imageType);

        $imageFolder = dirname($path);
        $imageFileName = basename($path);
        $allImages = collect($storage->allFiles($imageFolder));

        // Delete image files
        $imagesToDelete = $allImages->filter(function ($imagePath) use ($imageFileName) {
            return basename($imagePath) === $imageFileName;
        });
        $storage->delete($imagesToDelete->all());

        // Cleanup of empty folders
        $foldersInvolved = array_merge([$imageFolder], $storage->directories($imageFolder));
        foreach ($foldersInvolved as $directory) {
            if ($this->isFolderEmpty($storage, $directory)) {
                $storage->deleteDirectory($directory);
            }
        }

        return true;
    }

    /**
     * Check whether a folder is empty.
     */
    protected function isFolderEmpty(Storage $storage, string $path): bool
    {
        $files = $storage->files($path);
        $folders = $storage->directories($path);

        return count($files) === 0 && count($folders) === 0;
    }

    /**
     * Delete gallery and drawings that are not within HTML content of pages or page revisions.
     * Checks based off of only the image name.
     * Could be much improved to be more specific but kept it generic for now to be safe.
     *
     * Returns the path of the images that would be/have been deleted.
     */
    public function deleteUnusedImages(bool $checkRevisions = true, bool $dryRun = true)
    {
        $types = ['gallery', 'drawio'];
        $deletedPaths = [];

        Image::query()->whereIn('type', $types)
            ->chunk(1000, function ($images) use ($checkRevisions, &$deletedPaths, $dryRun) {
                foreach ($images as $image) {
                    $searchQuery = '%' . basename($image->path) . '%';
                    $inPage = DB::table('pages')
                            ->where('html', 'like', $searchQuery)->count() > 0;

                    $inRevision = false;
                    if ($checkRevisions) {
                        $inRevision = DB::table('page_revisions')
                                ->where('html', 'like', $searchQuery)->count() > 0;
                    }

                    if (!$inPage && !$inRevision) {
                        $deletedPaths[] = $image->path;
                        if (!$dryRun) {
                            $this->destroy($image);
                        }
                    }
                }
            });

        return $deletedPaths;
    }

    /**
     * Convert an image URI to a Base64 encoded string.
     * Attempts to convert the URL to a system storage url then
     * fetch the data from the disk or storage location.
     * Returns null if the image data cannot be fetched from storage.
     *
     * @throws FileNotFoundException
     */
    public function imageUriToBase64(string $uri): ?string
    {
        $storagePath = $this->imageUrlToStoragePath($uri);
        if (empty($uri) || is_null($storagePath)) {
            return null;
        }

        $storagePath = $this->adjustPathForStorageDisk($storagePath);
        $storage = $this->getStorageDisk();
        $imageData = null;
        if ($storage->exists($storagePath)) {
            $imageData = $storage->get($storagePath);
        }

        if (is_null($imageData)) {
            return null;
        }

        $extension = pathinfo($uri, PATHINFO_EXTENSION);
        if ($extension === 'svg') {
            $extension = 'svg+xml';
        }

        return 'data:image/' . $extension . ';base64,' . base64_encode($imageData);
    }

    /**
     * Check if the given path exists and is accessible in the local secure image system.
     * Returns false if local_secure is not in use, if the file does not exist, if the
     * file is likely not a valid image, or if permission does not allow access.
     */
    public function pathAccessibleInLocalSecure(string $imagePath): bool
    {
        /** @var FilesystemAdapter $disk */
        $disk = $this->getStorageDisk('gallery');

        if ($this->usingSecureRestrictedImages() && !$this->checkUserHasAccessToRelationOfImageAtPath($imagePath)) {
            return false;
        }

        // Check local_secure is active
        return $this->usingSecureImages()
            && $disk instanceof FilesystemAdapter
            // Check the image file exists
            && $disk->exists($imagePath)
            // Check the file is likely an image file
            && strpos($disk->getMimetype($imagePath), 'image/') === 0;
    }

    /**
     * Check that the current user has access to the relation
     * of the image at the given path.
     */
    protected function checkUserHasAccessToRelationOfImageAtPath(string $path): bool
    {
        // Strip thumbnail element from path if existing
        $originalPathSplit = array_filter(explode('/', $path), function(string $part) {
            $resizedDir = (strpos($part, 'thumbs-') === 0 || strpos($part, 'scaled-') === 0);
            $missingExtension = strpos($part, '.') === false;
            return !($resizedDir && $missingExtension);
        });

        // Build a database-format image path and search for the image entry
        $fullPath = '/uploads/images/' . ltrim(implode('/', $originalPathSplit), '/');
        $image = Image::query()->where('path', '=', $fullPath)->first();

        if (is_null($image)) {
            return false;
        }

        $imageType = $image->type;

        // Allow user or system (logo) images
        // (No specific relation control but may still have access controlled by auth)
        if ($imageType === 'user' || $imageType === 'system') {
            return true;
        }

        if ($imageType === 'gallery' || $imageType === 'drawio') {
            return Page::visible()->where('id', '=', $image->uploaded_to)->exists();
        }

        if ($imageType === 'cover_book') {
            return Book::visible()->where('id', '=', $image->uploaded_to)->exists();
        }

        if ($imageType === 'cover_bookshelf') {
            return Bookshelf::visible()->where('id', '=', $image->uploaded_to)->exists();
        }

        return false;
    }

    /**
     * For the given path, if existing, provide a response that will stream the image contents.
     */
    public function streamImageFromStorageResponse(string $imageType, string $path): StreamedResponse
    {
        $disk = $this->getStorageDisk($imageType);

        return $disk->response($path);
    }

    /**
     * Check if the given image extension is supported by BookStack.
     * The extension must not be altered in this function. This check should provide a guarantee
     * that the provided extension is safe to use for the image to be saved.
     */
    public static function isExtensionSupported(string $extension): bool
    {
        return in_array($extension, static::$supportedExtensions);
    }

    /**
     * Get a storage path for the given image URL.
     * Ensures the path will start with "uploads/images".
     * Returns null if the url cannot be resolved to a local URL.
     */
    private function imageUrlToStoragePath(string $url): ?string
    {
        $url = ltrim(trim($url), '/');

        // Handle potential relative paths
        $isRelative = strpos($url, 'http') !== 0;
        if ($isRelative) {
            if (strpos(strtolower($url), 'uploads/images') === 0) {
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
            if (strpos(strtolower($url), $potentialBasePath) === 0) {
                return 'uploads/images/' . trim(substr($url, strlen($potentialBasePath)), '/');
            }
        }

        return null;
    }

    /**
     * Gets a public facing url for an image by checking relevant environment variables.
     * If s3-style store is in use it will default to guessing a public bucket URL.
     */
    private function getPublicUrl(string $filePath): string
    {
        if (is_null($this->storageUrl)) {
            $storageUrl = config('filesystems.url');

            // Get the standard public s3 url if s3 is set as storage type
            // Uses the nice, short URL if bucket name has no periods in otherwise the longer
            // region-based url will be used to prevent http issues.
            if ($storageUrl == false && config('filesystems.images') === 's3') {
                $storageDetails = config('filesystems.disks.s3');
                if (strpos($storageDetails['bucket'], '.') === false) {
                    $storageUrl = 'https://' . $storageDetails['bucket'] . '.s3.amazonaws.com';
                } else {
                    $storageUrl = 'https://s3-' . $storageDetails['region'] . '.amazonaws.com/' . $storageDetails['bucket'];
                }
            }

            $this->storageUrl = $storageUrl;
        }

        $basePath = ($this->storageUrl == false) ? url('/') : $this->storageUrl;

        return rtrim($basePath, '/') . $filePath;
    }
}
