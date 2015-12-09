<?php namespace BookStack\Services;

use BookStack\Image;
use BookStack\User;
use Intervention\Image\ImageManager;
use Illuminate\Contracts\Filesystem\Factory as FileSystem;
use Illuminate\Contracts\Filesystem\Filesystem as FileSystemInstance;
use Illuminate\Contracts\Cache\Repository as Cache;
use Setting;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService
{

    protected $imageTool;
    protected $fileSystem;
    protected $cache;

    /**
     * @var FileSystemInstance
     */
    protected $storageInstance;
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
        $this->fileSystem = $fileSystem;
        $this->cache = $cache;
    }

    /**
     * Saves a new image from an upload.
     * @param UploadedFile $uploadedFile
     * @param  string      $type
     * @return mixed
     */
    public function saveNewFromUpload(UploadedFile $uploadedFile, $type)
    {
        $imageName = $uploadedFile->getClientOriginalName();
        $imageData = file_get_contents($uploadedFile->getRealPath());
        return $this->saveNew($imageName, $imageData, $type);
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
        if($imageData === false) throw new \Exception('Cannot get image from ' . $url);
        return $this->saveNew($imageName, $imageData, $type);
    }

    /**
     * Saves a new image
     * @param string $imageName
     * @param string $imageData
     * @param string $type
     * @return Image
     */
    private function saveNew($imageName, $imageData, $type)
    {
        $storage = $this->getStorage();
        $secureUploads = Setting::get('app-secure-images');
        $imageName = str_replace(' ', '-', $imageName);

        if ($secureUploads) $imageName = str_random(16) . '-' . $imageName;

        $imagePath = '/uploads/images/' . $type . '/' . Date('Y-m-M') . '/';
        while ($storage->exists($imagePath . $imageName)) {
            $imageName = str_random(3) . $imageName;
        }
        $fullPath = $imagePath . $imageName;

        $storage->put($fullPath, $imageData);

        $userId = auth()->user()->id;
        $image = Image::forceCreate([
            'name'       => $imageName,
            'path'       => $fullPath,
            'url'        => $this->getPublicUrl($fullPath),
            'type'       => $type,
            'created_by' => $userId,
            'updated_by' => $userId
        ]);

        return $image;
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
     * @return string
     */
    public function getThumbnail(Image $image, $width = 220, $height = 220, $keepRatio = false)
    {
        $thumbDirName = '/' . ($keepRatio ? 'scaled-' : 'thumbs-') . $width . '-' . $height . '/';
        $thumbFilePath = dirname($image->path) . $thumbDirName . basename($image->path);

        if ($this->cache->has('images-' . $image->id . '-' . $thumbFilePath) && $this->cache->get('images-' . $thumbFilePath)) {
            return $this->getPublicUrl($thumbFilePath);
        }

        $storage = $this->getStorage();

        if ($storage->exists($thumbFilePath)) {
            return $this->getPublicUrl($thumbFilePath);
        }

        // Otherwise create the thumbnail
        $thumb = $this->imageTool->make($storage->get($image->path));
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
        $this->cache->put('images-' . $image->id . '-' . $thumbFilePath, $thumbFilePath, 60 * 72);

        return $this->getPublicUrl($thumbFilePath);
    }

    /**
     * Destroys an Image object along with its files and thumbnails.
     * @param Image $image
     * @return bool
     */
    public function destroyImage(Image $image)
    {
        $storage = $this->getStorage();

        $imageFolder = dirname($image->path);
        $imageFileName = basename($image->path);
        $allImages = collect($storage->allFiles($imageFolder));

        $imagesToDelete = $allImages->filter(function ($imagePath) use ($imageFileName) {
            $expectedIndex = strlen($imagePath) - strlen($imageFileName);
            return strpos($imagePath, $imageFileName) === $expectedIndex;
        });

        $storage->delete($imagesToDelete->all());

        // Cleanup of empty folders
        foreach ($storage->directories($imageFolder) as $directory) {
            if ($this->isFolderEmpty($directory)) $storage->deleteDirectory($directory);
        }
        if ($this->isFolderEmpty($imageFolder)) $storage->deleteDirectory($imageFolder);

        $image->delete();
        return true;
    }

    /**
     * Save a gravatar image and set a the profile image for a user.
     * @param User $user
     * @param int  $size
     * @return mixed
     */
    public function saveUserGravatar(User $user, $size = 500)
    {
        if (!env('USE_GRAVATAR', false)) return false;
        $emailHash = md5(strtolower(trim($user->email)));
        $url = 'http://www.gravatar.com/avatar/' . $emailHash . '?s=' . $size . '&d=identicon';
        $imageName = str_replace(' ', '-', $user->name . '-gravatar.png');
        $image = $this->saveNewFromUrl($url, 'user', $imageName);
        $image->created_by = $user->id;
        $image->save();
        $user->avatar()->associate($image);
        $user->save();
        return $image;
    }

    /**
     * Get the storage that will be used for storing images.
     * @return FileSystemInstance
     */
    private function getStorage()
    {
        if ($this->storageInstance !== null) return $this->storageInstance;

        $storageType = env('STORAGE_TYPE');
        $this->storageInstance = $this->fileSystem->disk($storageType);

        return $this->storageInstance;
    }

    /**
     * Check whether or not a folder is empty.
     * @param $path
     * @return int
     */
    private function isFolderEmpty($path)
    {
        $files = $this->getStorage()->files($path);
        $folders = $this->getStorage()->directories($path);
        return count($files) === 0 && count($folders) === 0;
    }

    /**
     * Gets a public facing url for an image by checking relevant environment variables.
     * @param $filePath
     * @return string
     */
    private function getPublicUrl($filePath)
    {
        if ($this->storageUrl === null) {
            $storageUrl = env('STORAGE_URL');

            // Get the standard public s3 url if s3 is set as storage type
            if ($storageUrl == false && env('STORAGE_TYPE') === 's3') {
                $storageDetails = config('filesystems.disks.s3');
                $storageUrl = 'https://s3-' . $storageDetails['region'] . '.amazonaws.com/' . $storageDetails['bucket'];
            }

            $this->storageUrl = $storageUrl;
        }

        return ($this->storageUrl == false ? '' : rtrim($this->storageUrl, '/')) . $filePath;
    }


}