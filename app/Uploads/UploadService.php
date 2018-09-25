<?php namespace BookStack\Uploads;

use Illuminate\Contracts\Filesystem\Factory as FileSystem;
use Illuminate\Contracts\Filesystem\Filesystem as FileSystemInstance;

abstract class UploadService
{

    /**
     * @var FileSystem
     */
    protected $fileSystem;


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
        $storageType = config('filesystems.default');
        return $this->fileSystem->disk($storageType);
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
}
