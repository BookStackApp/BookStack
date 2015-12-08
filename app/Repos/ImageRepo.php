<?php namespace BookStack\Repos;


use BookStack\Image;
use Illuminate\Contracts\Filesystem\Filesystem as FileSystemInstance;
use Intervention\Image\ImageManager as ImageTool;
use Illuminate\Contracts\Filesystem\Factory as FileSystem;
use Illuminate\Contracts\Cache\Repository as Cache;
use Setting;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageRepo
{

    protected $image;
    protected $imageTool;
    protected $fileSystem;
    protected $cache;

    /**
     * @var FileSystemInstance
     */
    protected $storageInstance;
    protected $storageUrl;


    /**
     * ImageRepo constructor.
     * @param Image      $image
     * @param ImageTool  $imageTool
     * @param FileSystem $fileSystem
     * @param Cache      $cache
     */
    public function __construct(Image $image, ImageTool $imageTool, FileSystem $fileSystem, Cache $cache)
    {
        $this->image = $image;
        $this->imageTool = $imageTool;
        $this->fileSystem = $fileSystem;
        $this->cache = $cache;
    }


    /**
     * Get an image with the given id.
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->image->findOrFail($id);
    }


    /**
     * Gets a load images paginated, filtered by image type.
     * @param string $type
     * @param int    $page
     * @param int    $pageSize
     * @return array
     */
    public function getPaginatedByType($type, $page = 0, $pageSize = 24)
    {
        $images = $this->image->where('type', '=', strtolower($type))
            ->orderBy('created_at', 'desc')->skip($pageSize * $page)->take($pageSize + 1)->get();
        $hasMore = count($images) > $pageSize;

        $returnImages = $images->take(24);
        $returnImages->each(function ($image) {
            $this->loadThumbs($image);
        });

        return [
            'images' => $returnImages,
            'hasMore' => $hasMore
        ];
    }

    /**
     * Save a new image into storage and return the new image.
     * @param UploadedFile $uploadFile
     * @param  string      $type
     * @return Image
     */
    public function saveNew(UploadedFile $uploadFile, $type)
    {
        $storage = $this->getStorage();
        $secureUploads = Setting::get('app-secure-images');
        $imageName = str_replace(' ', '-', $uploadFile->getClientOriginalName());

        if ($secureUploads) $imageName = str_random(16) . '-' . $imageName;

        $imagePath = '/uploads/images/' . $type . '/' . Date('Y-m-M') . '/';
        while ($storage->exists($imagePath . $imageName)) {
            $imageName = str_random(3) . $imageName;
        }
        $fullPath = $imagePath . $imageName;

        $storage->put($fullPath, file_get_contents($uploadFile->getRealPath()));

        $userId = auth()->user()->id;
        $image = $this->image->forceCreate([
            'name' => $imageName,
            'path' => $fullPath,
            'url' => $this->getPublicUrl($fullPath),
            'type' => $type,
            'created_by' => $userId,
            'updated_by' => $userId
        ]);

        $this->loadThumbs($image);
        return $image;
    }

    /**
     * Update the details of an image via an array of properties.
     * @param Image $image
     * @param array $updateDetails
     * @return Image
     */
    public function updateImageDetails(Image $image, $updateDetails)
    {
        $image->fill($updateDetails);
        $image->save();
        $this->loadThumbs($image);
        return $image;
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
     * Load thumbnails onto an image object.
     * @param Image $image
     */
    private function loadThumbs(Image $image)
    {
        $image->thumbs = [
            'gallery' => $this->getThumbnail($image, 150, 150),
            'display' => $this->getThumbnail($image, 840, 0, true)
        ];
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


}