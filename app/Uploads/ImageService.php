<?php

namespace BookStack\Uploads;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Page;
use BookStack\Exceptions\ImageUploadException;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImageService
{
    protected static array $supportedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function __construct(
        protected ImageStorage $storage,
        protected ImageResizer $resizer,
    ) {
    }

    /**
     * Saves a new image from an upload.
     *
     * @throws ImageUploadException
     */
    public function saveNewFromUpload(
        UploadedFile $uploadedFile,
        string $type,
        int $uploadedTo = 0,
        int $resizeWidth = null,
        int $resizeHeight = null,
        bool $keepRatio = true
    ): Image {
        $imageName = $uploadedFile->getClientOriginalName();
        $imageData = file_get_contents($uploadedFile->getRealPath());

        if ($resizeWidth !== null || $resizeHeight !== null) {
            $imageData = $this->resizer->resizeImageData($imageData, $resizeWidth, $resizeHeight, $keepRatio);
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
        $disk = $this->storage->getDisk($type);
        $secureUploads = setting('app-secure-images');
        $fileName = $this->storage->cleanImageFileName($imageName);

        $imagePath = '/uploads/images/' . $type . '/' . date('Y-m') . '/';

        while ($disk->exists($imagePath . $fileName)) {
            $fileName = Str::random(3) . $fileName;
        }

        $fullPath = $imagePath . $fileName;
        if ($secureUploads) {
            $fullPath = $imagePath . Str::random(16) . '-' . $fileName;
        }

        try {
            $disk->put($fullPath, $imageData, true);
        } catch (Exception $e) {
            Log::error('Error when attempting image upload:' . $e->getMessage());

            throw new ImageUploadException(trans('errors.path_not_writable', ['filePath' => $fullPath]));
        }

        $imageDetails = [
            'name'        => $imageName,
            'path'        => $fullPath,
            'url'         => $this->storage->getPublicUrl($fullPath),
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
     * Replace an existing image file in the system using the given file.
     */
    public function replaceExistingFromUpload(string $path, string $type, UploadedFile $file): void
    {
        $imageData = file_get_contents($file->getRealPath());
        $disk = $this->storage->getDisk($type);
        $disk->put($path, $imageData);
    }

    /**
     * Get the raw data content from an image.
     *
     * @throws Exception
     */
    public function getImageData(Image $image): string
    {
        $disk = $this->storage->getDisk();

        return $disk->get($image->path);
    }

    /**
     * Destroy an image along with its revisions, thumbnails and remaining folders.
     *
     * @throws Exception
     */
    public function destroy(Image $image): void
    {
        $disk = $this->storage->getDisk($image->type);
        $disk->destroyAllMatchingNameFromPath($image->path);
        $image->delete();
    }

    /**
     * Delete gallery and drawings that are not within HTML content of pages or page revisions.
     * Checks based off of only the image name.
     * Could be much improved to be more specific but kept it generic for now to be safe.
     *
     * Returns the path of the images that would be/have been deleted.
     */
    public function deleteUnusedImages(bool $checkRevisions = true, bool $dryRun = true): array
    {
        $types = ['gallery', 'drawio'];
        $deletedPaths = [];

        Image::query()->whereIn('type', $types)
            ->chunk(1000, function ($images) use ($checkRevisions, &$deletedPaths, $dryRun) {
                /** @var Image $image */
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
     */
    public function imageUrlToBase64(string $url): ?string
    {
        $storagePath = $this->storage->urlToPath($url);
        if (empty($url) || is_null($storagePath)) {
            return null;
        }

        // Apply access control when local_secure_restricted images are active
        if ($this->storage->usingSecureRestrictedImages()) {
            if (!$this->checkUserHasAccessToRelationOfImageAtPath($storagePath)) {
                return null;
            }
        }

        $disk = $this->storage->getDisk();
        $imageData = null;
        if ($disk->exists($storagePath)) {
            $imageData = $disk->get($storagePath);
        }

        if (is_null($imageData)) {
            return null;
        }

        $extension = pathinfo($url, PATHINFO_EXTENSION);
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
        $disk = $this->storage->getDisk('gallery');

        if ($this->storage->usingSecureRestrictedImages() && !$this->checkUserHasAccessToRelationOfImageAtPath($imagePath)) {
            return false;
        }

        // Check local_secure is active
        return $disk->usingSecureImages()
            // Check the image file exists
            && $disk->exists($imagePath)
            // Check the file is likely an image file
            && str_starts_with($disk->mimeType($imagePath), 'image/');
    }

    /**
     * Check that the current user has access to the relation
     * of the image at the given path.
     */
    protected function checkUserHasAccessToRelationOfImageAtPath(string $path): bool
    {
        if (str_starts_with($path, 'uploads/images/')) {
            $path = substr($path, 15);
        }

        // Strip thumbnail element from path if existing
        $originalPathSplit = array_filter(explode('/', $path), function (string $part) {
            $resizedDir = (str_starts_with($part, 'thumbs-') || str_starts_with($part, 'scaled-'));
            $missingExtension = !str_contains($part, '.');

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
        $disk = $this->storage->getDisk($imageType);

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
}
