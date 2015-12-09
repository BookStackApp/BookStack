<?php namespace BookStack\Repos;


use BookStack\Image;
use BookStack\Services\ImageService;
use Setting;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageRepo
{

    protected $image;
    protected $imageService;

    /**
     * ImageRepo constructor.
     * @param Image        $image
     * @param ImageService $imageService
     */
    public function __construct(Image $image, ImageService $imageService)
    {
        $this->image = $image;
        $this->imageService = $imageService;
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
     * @param bool|int   $userFilter
     * @return array
     */
    public function getPaginatedByType($type, $page = 0, $pageSize = 24, $userFilter = false)
    {
        $images = $this->image->where('type', '=', strtolower($type));

        if ($userFilter !== false) {
            $images = $images->where('created_by', '=', $userFilter);
        }

        $images = $images->orderBy('created_at', 'desc')->skip($pageSize * $page)->take($pageSize + 1)->get();
        $hasMore = count($images) > $pageSize;

        $returnImages = $images->take(24);
        $returnImages->each(function ($image) {
            $this->loadThumbs($image);
        });

        return [
            'images'  => $returnImages,
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
        $image = $this->imageService->saveNewFromUpload($uploadFile, $type);
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
        $this->imageService->destroyImage($image);
        return true;
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
        return $this->imageService->getThumbnail($image, $width, $height, $keepRatio);
    }


}