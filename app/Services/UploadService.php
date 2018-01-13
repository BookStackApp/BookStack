<?php namespace BookStack\Services;

use Illuminate\Contracts\Filesystem\Factory as FileSystem;
use Illuminate\Contracts\Filesystem\Filesystem as FileSystemInstance;

class UploadService
{

    /**
     * @var FileSystem
     */
    protected $fileSystem;

    /**
     * @var FileSystemInstance
     */
    protected $storageInstance;


    /**
     * FileService constructor.
     * @param $fileSystem
     */
    public function __construct(FileSystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * Get the storage that will be used for storing images.
     * @return FileSystemInstance
     */
    protected function getStorage()
    {
        if ($this->storageInstance !== null) return $this->storageInstance;

        $storageType = config('filesystems.default');
        $this->storageInstance = $this->fileSystem->disk($storageType);

        return $this->storageInstance;
    }

    /**
     * Check whether or not a folder is empty.
     * @param $path
     * @return bool
     */
    protected function isFolderEmpty($path)
    {
        $files = $this->getStorage()->files($path);
        $folders = $this->getStorage()->directories($path);
        return (count($files) === 0 && count($folders) === 0);
    }

    /**
     * Check if using a local filesystem.
     * @return bool
     */
    protected function isLocal()
    {
        return strtolower(config('filesystems.default')) === 'local';
    }
}