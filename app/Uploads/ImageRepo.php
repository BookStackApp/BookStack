<?php namespace BookStack\Uploads;

use BookStack\Auth\Permissions\PermissionService;
use BookStack\Entities\Page;
use BookStack\Http\Requests\Request;
use Illuminate\Database\Eloquent\Builder;
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
    public function __construct(
        Image $image,
        ImageService $imageService,
        PermissionService $permissionService,
        Page $page
    )
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
     * @param bool $filterOnPage
     * @return array
     */
    private function returnPaginated($query, $page = 1, $pageSize = 24)
    {
        $images = $query->orderBy('created_at', 'desc')->skip($pageSize * ($page - 1))->take($pageSize + 1)->get();
        $hasMore = count($images) > $pageSize;

        $returnImages = $images->take($pageSize);
        $returnImages->each(function ($image) {
            $this->loadThumbs($image);
        });

        return [
            'images'  => $returnImages,
            'has_more' => $hasMore
        ];
    }

    /**
     * Fetch a list of images in a paginated format, filtered by image type.
     * Can be filtered by uploaded to and also by name.
     * @param string $type
     * @param int $page
     * @param int $pageSize
     * @param int $uploadedTo
     * @param string|null $search
     * @param callable|null $whereClause
     * @return array
     */
    public function getPaginatedByType(
        string $type,
        int $page = 0,
        int $pageSize = 24,
        int $uploadedTo = null,
        string $search = null,
        callable $whereClause = null
    )
    {
        $imageQuery = $this->image->newQuery()->where('type', '=', strtolower($type));

        if ($uploadedTo !== null) {
            $imageQuery = $imageQuery->where('uploaded_to', '=', $uploadedTo);
        }

        if ($search !== null) {
            $imageQuery = $imageQuery->where('name', 'LIKE', '%' . $search . '%');
        }

        // Filter by page access if gallery
        if ($type === 'gallery') {
            $imageQuery = $this->restrictionService->filterRelatedEntity('page', $imageQuery, 'images', 'uploaded_to');
        }

        // Filter by entity if cover
        if (strpos($type, 'cover_') === 0) {
            $entityType = explode('_', $type)[1];
            $imageQuery = $this->restrictionService->filterRelatedEntity($entityType, $imageQuery, 'images', 'uploaded_to');
        }

        if ($whereClause !== null) {
            $imageQuery = $imageQuery->where($whereClause);
        }

        return $this->returnPaginated($imageQuery, $page, $pageSize);
    }

    /**
     * Get paginated gallery images within a specific page or book.
     * @param string $type
     * @param string $filterType
     * @param int $page
     * @param int $pageSize
     * @param int|null $uploadedTo
     * @param string|null $search
     * @return array
     */
    public function getEntityFiltered(
        string $type,
        string $filterType = null,
        int $page = 0,
        int $pageSize = 24,
        int $uploadedTo = null,
        string $search = null
    )
    {
        $contextPage = $this->page->findOrFail($uploadedTo);
        $parentFilter = null;

        if ($filterType === 'book' || $filterType === 'page') {
            $parentFilter = function(Builder $query) use ($filterType, $contextPage) {
                if ($filterType === 'page') {
                    $query->where('uploaded_to', '=', $contextPage->id);
                } elseif ($filterType === 'book') {
                    $validPageIds = $contextPage->book->pages()->get(['id'])->pluck('id')->toArray();
                    $query->whereIn('uploaded_to', $validPageIds);
                }
            };
        }

        return $this->getPaginatedByType($type, $page, $pageSize, null, $search, $parentFilter);
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
        // TODO - To delete?
        $validTypes = ['gallery', 'cover', 'system', 'user'];
        return in_array($type, $validTypes);
    }

    /**
     * Get the validation rules for image files.
     * @return string
     */
    public function getImageValidationRules()
    {
        return 'image_extension|no_double_extension|mimes:jpeg,png,gif,bmp,webp,tiff';
    }
}
