<?php namespace BookStack\Services;

use BookStack\Exceptions\ImageUploadException;
use BookStack\Image;
use BookStack\User;
use Exception;
use Intervention\Image\Exception\NotSupportedException;
use Intervention\Image\ImageManager;
use Illuminate\Contracts\Filesystem\Factory as FileSystem;
use Illuminate\Contracts\Filesystem\Filesystem as FileSystemInstance;
use Illuminate\Contracts\Cache\Repository as Cache;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService extends UploadService
{

    protected $imageTool;
    protected $cache;
    protected $storageUrl;

    /**
     * ImageService constructor.
     * @param $imageTool
     * @param $fileSystem
     * @param $cache
     */
    public function __construct(ImageManager $imageTool, FileSystem $fileSystem, Cache $cache)
    {
        $this->imageTool = $imageTool;
        $this->cache = $cache;
        parent::__construct($fileSystem);
    }

    /**
     * Get the storage that will be used for storing images.
     * @param string $type
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function getStorage($type = '')
    {
        $storageType = config('filesystems.default');

        // Override default location if set to local public to ensure not visible.
        if ($type === 'system' && $storageType === 'local_secure') {
            $storageType = 'local';
        }

        return $this->fileSystem->disk($storageType);
    }

    /**
     * Saves a new image from an upload.
     * @param UploadedFile $uploadedFile
     * @param  string $type
     * @param int $uploadedTo
     * @return mixed
     * @throws ImageUploadException
     */
    public function saveNewFromUpload(UploadedFile $uploadedFile, $type, $uploadedTo = 0)
    {
        $imageName = $uploadedFile->getClientOriginalName();
        $imageData = file_get_contents($uploadedFile->getRealPath());
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
     * Replace the data for an image via a Base64 encoded string.
     * @param Image $image
     * @param string $base64Uri
     * @return Image
     * @throws ImageUploadException
     */
    public function replaceImageDataFromBase64Uri(Image $image, string $base64Uri)
    {
        $splitData = explode(';base64,', $base64Uri);
        if (count($splitData) < 2) {
            throw new ImageUploadException("Invalid base64 image data provided");
        }
        $data = base64_decode($splitData[1]);
        $storage = $this->getStorage();

        try {
            $storage->put($image->path, $data);
        } catch (Exception $e) {
            throw new ImageUploadException(trans('errors.path_not_writable', ['filePath' => $image->path]));
        }

        return $image;
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
        $imageData = file_get_contents($url);
        if ($imageData === false) {
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

        if ($secureUploads) {
            $imageName = str_random(16) . '-' . $imageName;
        }

        $imagePath = '/uploads/images/' . $type . '/' . Date('Y-m-M') . '/';

        while ($storage->exists($imagePath . $imageName)) {
            $imageName = str_random(3) . $imageName;
        }
        $fullPath = $imagePath . $imageName;

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

        $image = (new Image());
        $image->forceFill($imageDetails)->save();
        return $image;
    }

    /**
     * Get the storage path, Dependant of storage type.
     * @param Image $image
     * @return mixed|string
     */
    protected function getPath(Image $image)
    {
        return $image->path;
    }

    /**
     * Checks if the image is a gif. Returns true if it is, else false.
     * @param Image $image
     * @return boolean
     */
    protected function isGif(Image $image)
    {
        return strtolower(pathinfo($this->getPath($image), PATHINFO_EXTENSION)) === 'gif';
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
            return $this->getPublicUrl($this->getPath($image));
        }

        $thumbDirName = '/' . ($keepRatio ? 'scaled-' : 'thumbs-') . $width . '-' . $height . '/';
        $imagePath = $this->getPath($image);
        $thumbFilePath = dirname($imagePath) . $thumbDirName . basename($imagePath);

        if ($this->cache->has('images-' . $image->id . '-' . $thumbFilePath) && $this->cache->get('images-' . $thumbFilePath)) {
            return $this->getPublicUrl($thumbFilePath);
        }

        $storage = $this->getStorage($image->type);
        if ($storage->exists($thumbFilePath)) {
            return $this->getPublicUrl($thumbFilePath);
        }

        try {
            $thumb = $this->imageTool->make($storage->get($imagePath));
        } catch (Exception $e) {
            if ($e instanceof \ErrorException || $e instanceof NotSupportedException) {
                throw new ImageUploadException(trans('errors.cannot_create_thumbs'));
            }
            throw $e;
        }

        if ($keepRatio) {
            $thumb->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        } else {
            $thumb->fit($width, $height);
        }

        $thumbData = (string)$thumb->encode();
        $storage->put($thumbFilePath, $thumbData);
        $storage->setVisibility($thumbFilePath, 'public');
        $this->cache->put('images-' . $image->id . '-' . $thumbFilePath, $thumbFilePath, 60 * 72);

        return $this->getPublicUrl($thumbFilePath);
    }

    /**
     * Get the raw data content from an image.
     * @param Image $image
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getImageData(Image $image)
    {
        $imagePath = $this->getPath($image);
        $storage = $this->getStorage();
        return $storage->get($imagePath);
    }

    /**
     * Destroys an Image object along with its files and thumbnails.
     * @param Image $image
     * @return bool
     * @throws Exception
     */
    public function destroyImage(Image $image)
    {
        $storage = $this->getStorage();

        $imageFolder = dirname($this->getPath($image));
        $imageFileName = basename($this->getPath($image));
        $allImages = collect($storage->allFiles($imageFolder));

        $imagesToDelete = $allImages->filter(function ($imagePath) use ($imageFileName) {
            $expectedIndex = strlen($imagePath) - strlen($imageFileName);
            return strpos($imagePath, $imageFileName) === $expectedIndex;
        });

        $storage->delete($imagesToDelete->all());

        // Cleanup of empty folders
        foreach ($storage->directories($imageFolder) as $directory) {
            if ($this->isFolderEmpty($directory)) {
                $storage->deleteDirectory($directory);
            }
        }
        if ($this->isFolderEmpty($imageFolder)) {
            $storage->deleteDirectory($imageFolder);
        }

        $image->delete();
        return true;
    }

    /**
     * Save a gravatar image and set a the profile image for a user.
     * @param User $user
     * @param int $size
     * @return mixed
     * @throws Exception
     */
    public function saveUserGravatar(User $user, $size = 500)
    {
        $emailHash = md5(strtolower(trim($user->email)));
        $url = 'https://www.gravatar.com/avatar/' . $emailHash . '?s=' . $size . '&d=identicon';
        $imageName = str_replace(' ', '-', $user->name . '-gravatar.png');
        $image = $this->saveNewFromUrl($url, 'user', $imageName);
        $image->created_by = $user->id;
        $image->updated_by = $user->id;
        $image->save();
        return $image;
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
        $base = baseUrl('/');
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
                $ch = curl_init();
                curl_setopt_array($ch, [CURLOPT_URL => $uri, CURLOPT_RETURNTRANSFER => 1, CURLOPT_CONNECTTIMEOUT => 5]);
                $imageData = curl_exec($ch);
                $err = curl_error($ch);
                curl_close($ch);
                if ($err) {
                    throw new \Exception("Image fetch failed, Received error: " . $err);
                }
            } catch (\Exception $e) {
            }
        }

        if ($imageData === null) {
            return null;
        }

        return 'data:image/' . pathinfo($uri, PATHINFO_EXTENSION) . ';base64,' . base64_encode($imageData);
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
            if ($storageUrl == false && config('filesystems.default') === 's3') {
                $storageDetails = config('filesystems.disks.s3');
                if (strpos($storageDetails['bucket'], '.') === false) {
                    $storageUrl = 'https://' . $storageDetails['bucket'] . '.s3.amazonaws.com';
                } else {
                    $storageUrl = 'https://s3-' . $storageDetails['region'] . '.amazonaws.com/' . $storageDetails['bucket'];
                }
            }
            $this->storageUrl = $storageUrl;
        }

        $basePath = ($this->storageUrl == false) ? baseUrl('/') : $this->storageUrl;
        return rtrim($basePath, '/') . $filePath;
    }
}
