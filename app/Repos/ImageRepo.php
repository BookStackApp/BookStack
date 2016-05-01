<?php namespace BookStack\Repos;


use BookStack\Image;
use BookStack\Page;
use BookStack\Services\ImageService;
use BookStack\Services\PermissionService;
use Setting;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageRepo
{

    protected $image;
    protected $imageService;
    protected $restictionService;
    protected $page;

    /**
     * ImageRepo constructor.
     * @param Image $image
     * @param ImageService $imageService
     * @param PermissionService $permissionService
     * @param Page $page
     */
    public function __construct(Image $image, ImageService $imageService, PermissionService $permissionService, Page $page)
    {
        $this->image = $image;
        $this->imageService = $imageService;
        $this->restictionService = $permissionService;
        $this->page = $page;
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
     * Execute a paginated query, returning in a standard format.
     * Also runs the query through the restriction system.
     * @param $query
     * @param int $page
     * @param int $pageSize
     * @return array
     */
    private function returnPaginated($query, $page = 0, $pageSize = 24)
    {
        $images = $this->restictionService->filterRelatedPages($query, 'images', 'uploaded_to');
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
     * Gets a load images paginated, filtered by image type.
     * @param string $type
     * @param int $page
     * @param int $pageSize
     * @param bool|int $userFilter
     * @return array
     */
    public function getPaginatedByType($type, $page = 0, $pageSize = 24, $userFilter = false)
    {
        $images = $this->image->where('type', '=', strtolower($type));

        if ($userFilter !== false) {
            $images = $images->where('created_by', '=', $userFilter);
        }

        return $this->returnPaginated($images, $page, $pageSize);
    }

    /**
     * Search for images by query, of a particular type.
     * @param string $type
     * @param int $page
     * @param int $pageSize
     * @param string $searchTerm
     * @return array
     */
    public function searchPaginatedByType($type, $page = 0, $pageSize = 24, $searchTerm)
    {
        $images = $this->image->where('type', '=', strtolower($type))->where('name', 'LIKE', '%' . $searchTerm . '%');
        return $this->returnPaginated($images, $page, $pageSize);
    }

    /**
     * Get gallery images with a particular filter criteria such as
     * being within the current book or page.
     * @param int $pagination
     * @param int $pageSize
     * @param $filter
     * @param $pageId
     * @return array
     */
    public function getGalleryFiltered($pagination = 0, $pageSize = 24, $filter, $pageId)
    {
        $images = $this->image->where('type', '=', 'gallery');

        $page = $this->page->findOrFail($pageId);

        if ($filter === 'page') {
            $images = $images->where('uploaded_to', '=', $page->id);
        } elseif ($filter === 'book') {
            $validPageIds = $page->book->pages->pluck('id')->toArray();
            $images = $images->whereIn('uploaded_to', $validPageIds);
        }

        return $this->returnPaginated($images, $pagination, $pageSize);
    }

    /**
     * Save a new image into storage and return the new image.
     * @param UploadedFile $uploadFile
     * @param  string $type
     * @param int $uploadedTo
     * @return Image
     */
    public function saveNew(UploadedFile $uploadFile, $type, $uploadedTo = 0)
    {
        $image = $this->imageService->saveNewFromUpload($uploadFile, $type, $uploadedTo);
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
     * @param int $width
     * @param int $height
     * @param bool $keepRatio
     * @return string
     */
    public function getThumbnail(Image $image, $width = 220, $height = 220, $keepRatio = false)
    {
        return $this->imageService->getThumbnail($image, $width, $height, $keepRatio);
    }


}