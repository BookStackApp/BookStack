<?php

namespace BookStack\Uploads;

use BookStack\Exceptions\ImageUploadException;
use Illuminate\Support\Facades\DB;
use ErrorException;
use Exception;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Filesystem\Factory as FileSystem;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem as FileSystemInstance;
use Illuminate\Contracts\Filesystem\Filesystem as Storage;
use Illuminate\Support\Str;
use Intervention\Image\Exception\NotSupportedException;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService
{
    protected $imageTool;
    protected $cache;
    protected $storageUrl;
    protected $image;
    protected $fileSystem;

    /**
     * ImageService constructor.
     */
    public function __construct(Image $image, ImageManager $imageTool, FileSystem $fileSystem, Cache $cache)
    {
        $this->image = $image;
        $this->imageTool = $imageTool;
        $this->fileSystem = $fileSystem;
        $this->cache = $cache;
    }

    /**
     * Get the storage that will be used for storing images.
     */
    protected function getStorage(string $type = ''): FileSystemInstance
    {
        $storageType = config('filesystems.images');

        // Ensure system images (App logo) are uploaded to a public space
        if ($type === 'system' && $storageType === 'local_secure') {
            $storageType = 'local';
        }

        return $this->fileSystem->disk($storageType);
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
        $storage = $this->getStorage($type);
        $secureUploads = setting('app-secure-images');
        $fileName = $this->cleanImageFileName($imageName);

        $imagePath = '/uploads/images/' . $type . '/' . date('Y-m') . '/';

        while ($storage->exists($imagePath . $fileName)) {
            $fileName = Str::random(3) . $fileName;
        }

        $fullPath = $imagePath . $fileName;
        if ($secureUploads) {
            $fullPath = $imagePath . Str::random(16) . '-' . $fileName;
        }

        try {
            $this->saveImageDataInPublicSpace($storage, $fullPath, $imageData);
        } catch (Exception $e) {
            \Log::error('Error when attempting image upload:' . $e->getMessage());

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

        $image = $this->image->newInstance();
        $image->forceFill($imageDetails)->save();

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
     * Get the thumbnail for an image.
     * If $keepRatio is true only the width will be used.
     * Checks the cache then storage to avoid creating / accessing the filesystem on every check.
     *
     * @param Image $image
     * @param int   $width
     * @param int   $height
     * @param bool  $keepRatio
     *
     * @throws Exception
     * @throws ImageUploadException
     *
     * @return string
     */
    public function getThumbnail(Image $image, $width = 220, $height = 220, $keepRatio = false)
    {
        if ($keepRatio && $this->isGif($image)) {
            return $this->getPublicUrl($image->path);
        }

        $thumbDirName = '/' . ($keepRatio ? 'scaled-' : 'thumbs-') . $width . '-' . $height . '/';
        $imagePath = $image->path;
        $thumbFilePath = dirname($imagePath) . $thumbDirName . basename($imagePath);

        if ($this->cache->has('images-' . $image->id . '-' . $thumbFilePath) && $this->cache->get('images-' . $thumbFilePath)) {
            return $this->getPublicUrl($thumbFilePath);
        }

        $storage = $this->getStorage($image->type);
        if ($storage->exists($thumbFilePath)) {
            return $this->getPublicUrl($thumbFilePath);
        }

        $thumbData = $this->resizeImage($storage->get($imagePath), $width, $height, $keepRatio);

        $this->saveImageDataInPublicSpace($storage, $thumbFilePath, $thumbData);
        $this->cache->put('images-' . $image->id . '-' . $thumbFilePath, $thumbFilePath, 60 * 60 * 72);

        return $this->getPublicUrl($thumbFilePath);
    }

    /**
     * Resize image data.
     *
     * @param string $imageData
     * @param int    $width
     * @param int    $height
     * @param bool   $keepRatio
     *
     * @throws ImageUploadException
     *
     * @return string
     */
    protected function resizeImage(string $imageData, $width = 220, $height = null, bool $keepRatio = true)
    {
        try {
            $thumb = $this->imageTool->make($imageData);
        } catch (Exception $e) {
            if ($e instanceof ErrorException || $e instanceof NotSupportedException) {
                throw new ImageUploadException(trans('errors.cannot_create_thumbs'));
            }

            throw $e;
        }

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
     * Get the raw data content from an image.
     *
     * @throws FileNotFoundException
     */
    public function getImageData(Image $image): string
    {
        $imagePath = $image->path;
        $storage = $this->getStorage();

        return $storage->get($imagePath);
    }

    /**
     * Destroy an image along with its revisions, thumbnails and remaining folders.
     *
     * @throws Exception
     */
    public function destroy(Image $image)
    {
        $this->destroyImagesFromPath($image->path);
        $image->delete();
    }

    /**
     * Destroys an image at the given path.
     * Searches for image thumbnails in addition to main provided path.
     */
    protected function destroyImagesFromPath(string $path): bool
    {
        $storage = $this->getStorage();

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
     * Check whether or not a folder is empty.
     */
    protected function isFolderEmpty(FileSystemInstance $storage, string $path): bool
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

        $this->image->newQuery()->whereIn('type', $types)
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
     * Convert a image URI to a Base64 encoded string.
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

        $storage = $this->getStorage();
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
        if ($this->storageUrl === null) {
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
