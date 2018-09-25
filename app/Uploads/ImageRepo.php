<?php namespace BookStack\Uploads;

use BookStack\Auth\Permissions\PermissionService;
use BookStack\Entities\Page;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageRepo
{

    protected $image;
    protected $imageService;
    protected $restrictionService;
    protected $page;

    /**
     * ImageRepo constructor.
     * @param Image $image
     * @param ImageService $imageService
     * @param \BookStack\Auth\Permissions\PermissionService $permissionService
     * @param \BookStack\Entities\Page $page
     */
    public function __construct(Image $image, ImageService $imageService, PermissionService $permissionService, Page $page)
    {
        $this->image = $image;
        $this->imageService = $imageService;
        $this->restrictionService = $permissionService;
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
        $images = $this->restrictionService->filterRelatedPages($query, 'images', 'uploaded_to');
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
    public function searchPaginatedByType($type, $searchTerm, $page = 0, $pageSize = 24)
    {
        $images = $this->image->where('type', '=', strtolower($type))->where('name', 'LIKE', '%' . $searchTerm . '%');
        return $this->returnPaginated($images, $page, $pageSize);
    }

    /**
     * Get gallery images with a particular filter criteria such as
     * being within the current book or page.
     * @param $filter
     * @param $pageId
     * @param int $pageNum
     * @param int $pageSize
     * @return array
     */
    public function getGalleryFiltered($filter, $pageId, $pageNum = 0, $pageSize = 24)
    {
        $images = $this->image->where('type', '=', 'gallery');

        $page = $this->page->findOrFail($pageId);

        if ($filter === 'page') {
            $images = $images->where('uploaded_to', '=', $page->id);
        } elseif ($filter === 'book') {
            $validPageIds = $page->book->pages->pluck('id')->toArray();
            $images = $images->whereIn('uploaded_to', $validPageIds);
        }

        return $this->returnPaginated($images, $pageNum, $pageSize);
    }

    /**
     * Save a new image into storage and return the new image.
     * @param UploadedFile $uploadFile
     * @param  string $type
     * @param int $uploadedTo
     * @return Image
     * @throws \BookStack\Exceptions\ImageUploadException
     * @throws \Exception
     */
    public function saveNew(UploadedFile $uploadFile, $type, $uploadedTo = 0)
    {
        $image = $this->imageService->saveNewFromUpload($uploadFile, $type, $uploadedTo);
        $this->loadThumbs($image);
        return $image;
    }

    /**
     * Save a drawing the the database;
     * @param string $base64Uri
     * @param int $uploadedTo
     * @return Image
     * @throws \BookStack\Exceptions\ImageUploadException
     */
    public function saveDrawing(string $base64Uri, int $uploadedTo)
    {
        $name = 'Drawing-' . user()->getShortName(40) . '-' . strval(time()) . '.png';
        $image = $this->imageService->saveNewFromBase64Uri($base64Uri, $name, 'drawio', $uploadedTo);
        return $image;
    }


    /**
     * Update the details of an image via an array of properties.
     * @param Image $image
     * @param array $updateDetails
     * @return Image
     * @throws \BookStack\Exceptions\ImageUploadException
     * @throws \Exception
     */
    public function updateImageDetails(Image $image, $updateDetails)
    {
        $image->fill($updateDetails);
        $image->save();
        $this->loadThumbs($image);
        return $image;
    }


    /**
     * Destroys an Image object along with its revisions, files and thumbnails.
     * @param Image $image
     * @return bool
     * @throws \Exception
     */
    public function destroyImage(Image $image)
    {
        $this->imageService->destroy($image);
        return true;
    }


    /**
     * Load thumbnails onto an image object.
     * @param Image $image
     * @throws \BookStack\Exceptions\ImageUploadException
     * @throws \Exception
     */
    protected function loadThumbs(Image $image)
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
     * @param Image $image
     * @param int $width
     * @param int $height
     * @param bool $keepRatio
     * @return string
     * @throws \BookStack\Exceptions\ImageUploadException
     * @throws \Exception
     */
    public function getThumbnail(Image $image, $width = 220, $height = 220, $keepRatio = false)
    {
        try {
            return $this->imageService->getThumbnail($image, $width, $height, $keepRatio);
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * Get the raw image data from an Image.
     * @param Image $image
     * @return null|string
     */
    public function getImageData(Image $image)
    {
        try {
            return $this->imageService->getImageData($image);
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * Check if the provided image type is valid.
     * @param $type
     * @return bool
     */
    public function isValidType($type)
    {
        $validTypes = ['gallery', 'cover', 'system', 'user'];
        return in_array($type, $validTypes);
    }
}
