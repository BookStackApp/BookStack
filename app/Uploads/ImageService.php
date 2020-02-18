<?php namespace BookStack\Uploads;

use BookStack\Auth\User;
use BookStack\Exceptions\HttpFetchException;
use BookStack\Exceptions\ImageUploadException;
use DB;
use Exception;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Filesystem\Factory as FileSystem;
use Illuminate\Support\Str;
use Intervention\Image\Exception\NotSupportedException;
use Intervention\Image\ImageManager;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService extends UploadService
{

    protected $imageTool;
    protected $cache;
    protected $storageUrl;
    protected $image;
    protected $http;

    /**
     * ImageService constructor.
     * @param Image $image
     * @param ImageManager $imageTool
     * @param FileSystem $fileSystem
     * @param Cache $cache
     * @param HttpFetcher $http
     */
    public function __construct(Image $image, ImageManager $imageTool, FileSystem $fileSystem, Cache $cache, HttpFetcher $http)
    {
        $this->image = $image;
        $this->imageTool = $imageTool;
        $this->cache = $cache;
        $this->http = $http;
        parent::__construct($fileSystem);
    }

    /**
     * Get the storage that will be used for storing images.
     * @param string $type
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function getStorage($type = '')
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
     * @param UploadedFile $uploadedFile
     * @param string $type
     * @param int $uploadedTo
     * @param int|null $resizeWidth
     * @param int|null $resizeHeight
     * @param bool $keepRatio
     * @return mixed
     * @throws ImageUploadException
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
     * @param string $base64Uri
     * @param string $name
     * @param string $type
     * @param int $uploadedTo
     * @return Image
     * @throws ImageUploadException
     */
    public function saveNewFromBase64Uri(string $base64Uri, string $name, string $type, $uploadedTo = 0)
    {
        $splitData = explode(';base64,', $base64Uri);
        if (count($splitData) < 2) {
            throw new ImageUploadException("Invalid base64 image data provided");
        }
        $data = base64_decode($splitData[1]);
        return $this->saveNew($name, $data, $type, $uploadedTo);
    }

    /**
     * Gets an image from url and saves it to the database.
     * @param             $url
     * @param string      $type
     * @param bool|string $imageName
     * @return mixed
     * @throws \Exception
     */
    private function saveNewFromUrl($url, $type, $imageName = false)
    {
        $imageName = $imageName ? $imageName : basename($url);
        try {
            $imageData = $this->http->fetch($url);
        } catch (HttpFetchException $exception) {
            throw new \Exception(trans('errors.cannot_get_image_from_url', ['url' => $url]));
        }
        return $this->saveNew($imageName, $imageData, $type);
    }

    /**
     * Saves a new image
     * @param string $imageName
     * @param string $imageData
     * @param string $type
     * @param int $uploadedTo
     * @return Image
     * @throws ImageUploadException
     */
    private function saveNew($imageName, $imageData, $type, $uploadedTo = 0)
    {
        $storage = $this->getStorage($type);
        $secureUploads = setting('app-secure-images');
        $imageName = str_replace(' ', '-', $imageName);

        $imagePath = '/uploads/images/' . $type . '/' . Date('Y-m') . '/';

        while ($storage->exists($imagePath . $imageName)) {
            $imageName = Str::random(3) . $imageName;
        }

        $fullPath = $imagePath . $imageName;
        if ($secureUploads) {
            $fullPath = $imagePath . Str::random(16) . '-' . $imageName;
        }

        try {
            $storage->put($fullPath, $imageData);
            $storage->setVisibility($fullPath, 'public');
        } catch (Exception $e) {
            throw new ImageUploadException(trans('errors.path_not_writable', ['filePath' => $fullPath]));
        }

        $imageDetails = [
            'name'       => $imageName,
            'path'       => $fullPath,
            'url'        => $this->getPublicUrl($fullPath),
            'type'       => $type,
            'uploaded_to' => $uploadedTo
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
     * Checks if the image is a gif. Returns true if it is, else false.
     * @param Image $image
     * @return boolean
     */
    protected function isGif(Image $image)
    {
        return strtolower(pathinfo($image->path, PATHINFO_EXTENSION)) === 'gif';
    }

    /**
     * Get the thumbnail for an image.
     * If $keepRatio is true only the width will be used.
     * Checks the cache then storage to avoid creating / accessing the filesystem on every check.
     * @param Image $image
     * @param int $width
     * @param int $height
     * @param bool $keepRatio
     * @return string
     * @throws Exception
     * @throws ImageUploadException
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

        $storage->put($thumbFilePath, $thumbData);
        $storage->setVisibility($thumbFilePath, 'public');
        $this->cache->put('images-' . $image->id . '-' . $thumbFilePath, $thumbFilePath, 60 * 60 * 72);

        return $this->getPublicUrl($thumbFilePath);
    }

    /**
     * Resize image data.
     * @param string $imageData
     * @param int $width
     * @param int $height
     * @param bool $keepRatio
     * @return string
     * @throws ImageUploadException
     */
    protected function resizeImage(string $imageData, $width = 220, $height = null, bool $keepRatio = true)
    {
        try {
            $thumb = $this->imageTool->make($imageData);
        } catch (Exception $e) {
            if ($e instanceof \ErrorException || $e instanceof NotSupportedException) {
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

        $thumbData = (string)$thumb->encode();

        // Use original image data if we're keeping the ratio
        // and the resizing does not save any space.
        if ($keepRatio && strlen($thumbData) > strlen($imageData)) {
            return $imageData;
        }

        return $thumbData;
    }

    /**
     * Get the raw data content from an image.
     * @param Image $image
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getImageData(Image $image)
    {
        $imagePath = $image->path;
        $storage = $this->getStorage();
        return $storage->get($imagePath);
    }

    /**
     * Destroy an image along with its revisions, thumbnails and remaining folders.
     * @param Image $image
     * @throws Exception
     */
    public function destroy(Image $image)
    {
        $this->destroyImagesFromPath($image->path);
        $image->delete();
    }

    /**
     * Destroys an image at the given path.
     * Searches for image thumbnails in addition to main provided path..
     * @param string $path
     * @return bool
     */
    protected function destroyImagesFromPath(string $path)
    {
        $storage = $this->getStorage();

        $imageFolder = dirname($path);
        $imageFileName = basename($path);
        $allImages = collect($storage->allFiles($imageFolder));

        // Delete image files
        $imagesToDelete = $allImages->filter(function ($imagePath) use ($imageFileName) {
            $expectedIndex = strlen($imagePath) - strlen($imageFileName);
            return strpos($imagePath, $imageFileName) === $expectedIndex;
        });
        $storage->delete($imagesToDelete->all());

        // Cleanup of empty folders
        $foldersInvolved = array_merge([$imageFolder], $storage->directories($imageFolder));
        foreach ($foldersInvolved as $directory) {
            if ($this->isFolderEmpty($directory)) {
                $storage->deleteDirectory($directory);
            }
        }

        return true;
    }

    /**
     * Save an avatar image from an external service.
     * @param \BookStack\Auth\User $user
     * @param int $size
     * @return Image
     * @throws Exception
     */
    public function saveUserAvatar(User $user, $size = 500)
    {
        $avatarUrl = $this->getAvatarUrl();
        $email = strtolower(trim($user->email));

        $replacements = [
            '${hash}' => md5($email),
            '${size}' => $size,
            '${email}' => urlencode($email),
        ];

        $userAvatarUrl = strtr($avatarUrl, $replacements);
        $imageName = str_replace(' ', '-', $user->name . '-avatar.png');
        $image = $this->saveNewFromUrl($userAvatarUrl, 'user', $imageName);
        $image->created_by = $user->id;
        $image->updated_by = $user->id;
        $image->uploaded_to = $user->id;
        $image->save();

        return $image;
    }

    /**
     * Check if fetching external avatars is enabled.
     * @return bool
     */
    public function avatarFetchEnabled()
    {
        $fetchUrl = $this->getAvatarUrl();
        return is_string($fetchUrl) && strpos($fetchUrl, 'http') === 0;
    }

    /**
     * Get the URL to fetch avatars from.
     * @return string|mixed
     */
    protected function getAvatarUrl()
    {
        $url = trim(config('services.avatar_url'));

        if (empty($url) && !config('services.disable_services')) {
            $url = 'https://www.gravatar.com/avatar/${hash}?s=${size}&d=identicon';
        }

        return $url;
    }

    /**
     * Delete gallery and drawings that are not within HTML content of pages or page revisions.
     * Checks based off of only the image name.
     * Could be much improved to be more specific but kept it generic for now to be safe.
     *
     * Returns the path of the images that would be/have been deleted.
     * @param bool $checkRevisions
     * @param bool $dryRun
     * @param array $types
     * @return array
     */
    public function deleteUnusedImages($checkRevisions = true, $dryRun = true, $types = ['gallery', 'drawio'])
    {
        $types = array_intersect($types, ['gallery', 'drawio']);
        $deletedPaths = [];

        $this->image->newQuery()->whereIn('type', $types)
            ->chunk(1000, function ($images) use ($types, $checkRevisions, &$deletedPaths, $dryRun) {
                foreach ($images as $image) {
                    $searchQuery = '%' . basename($image->path) . '%';
                    $inPage = DB::table('pages')
                         ->where('html', 'like', $searchQuery)->count() > 0;
                    $inRevision = false;
                    if ($checkRevisions) {
                        $inRevision =  DB::table('page_revisions')
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
     * Attempts to find locally via set storage method first.
     * @param string $uri
     * @return null|string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function imageUriToBase64(string $uri)
    {
        $isLocal = strpos(trim($uri), 'http') !== 0;

        // Attempt to find local files even if url not absolute
        $base = url('/');
        if (!$isLocal && strpos($uri, $base) === 0) {
            $isLocal = true;
            $uri = str_replace($base, '', $uri);
        }

        $imageData = null;

        if ($isLocal) {
            $uri = trim($uri, '/');
            $storage = $this->getStorage();
            if ($storage->exists($uri)) {
                $imageData = $storage->get($uri);
            }
        } else {
            try {
                $imageData = $this->http->fetch($uri);
            } catch (\Exception $e) {
            }
        }

        if ($imageData === null) {
            return null;
        }

        $extension = pathinfo($uri, PATHINFO_EXTENSION);
        if ($extension === 'svg') {
            $extension = 'svg+xml';
        }

        return 'data:image/' . $extension . ';base64,' . base64_encode($imageData);
    }

    /**
     * Gets a public facing url for an image by checking relevant environment variables.
     * @param string $filePath
     * @return string
     */
    private function getPublicUrl($filePath)
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
