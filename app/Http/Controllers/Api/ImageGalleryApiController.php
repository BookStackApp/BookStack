<?php

namespace BookStack\Http\Controllers\Api;

use BookStack\Uploads\Image;
use BookStack\Uploads\ImageRepo;
use Illuminate\Http\Request;

class ImageGalleryApiController extends ApiController
{
    protected array $fieldsToExpose = [
        'id', 'name', 'url', 'path', 'type', 'uploaded_to', 'created_by', 'updated_by',  'created_at', 'updated_at',
    ];

    public function __construct(
        protected ImageRepo $imageRepo
    ) {
    }

    protected function rules(): array
    {
        return [
            'create' => [
                'type'  => ['required', 'string', 'in:gallery,drawio'],
                'uploaded_to' => ['required', 'integer', 'exists:pages,id'],
                'image' => ['required', 'file', ...$this->getImageValidationRules()],
                'name'  => ['string', 'max:180'],
            ],
            'update' => [
                'name'  => ['string', 'max:180'],
            ]
        ];
    }

    /**
     * Get a listing of gallery images and drawings in the system.
     * Requires visibility of the content they're originally uploaded to.
     */
    public function list()
    {
        $images = Image::query()->scopes(['visible'])
            ->select($this->fieldsToExpose)
            ->whereIn('type', ['gallery', 'drawio']);

        return $this->apiListingResponse($images, [
            ...$this->fieldsToExpose
        ]);
    }

    /**
     * Create a new image in the system.
     */
    public function create(Request $request)
    {
        $data = $this->validate($request, $this->rules()['create']);

        $image = $this->imageRepo->saveNew($data['image'], $data['type'], $data['uploaded_to']);

        return response()->json($this->formatForSingleResponse($image));
    }

    /**
     * View the details of a single image.
     */
    public function read(string $id)
    {
        $image = $this->imageRepo->getById($id);
        $this->checkOwnablePermission('page-view', $image->getPage());

        return response()->json($this->formatForSingleResponse($image));
    }

    /**
     * Update an existing image in the system.
     */
    public function update(Request $request, string $id)
    {
        $data = $this->validate($request, $this->rules()['update']);
        $image = $this->imageRepo->getById($id);
        $this->checkOwnablePermission('page-view', $image->getPage());
        $this->checkOwnablePermission('image-update', $image);

        $this->imageRepo->updateImageDetails($image, $data);

        return response()->json($this->formatForSingleResponse($image));
    }

    /**
     * Delete an image from the system.
     */
    public function delete(string $id)
    {
        $image = $this->imageRepo->getById($id);
        $this->checkOwnablePermission('page-view', $image->getPage());
        $this->checkOwnablePermission('image-delete', $image);
        $this->imageRepo->destroyImage($image);

        return response('', 204);
    }

    /**
     * Format the given image model for single-result display.
     */
    protected function formatForSingleResponse(Image $image): array
    {
        $this->imageRepo->loadThumbs($image);
        $data = $image->getAttributes();
        $data['created_by'] = $image->createdBy;
        $data['updated_by'] = $image->updatedBy;
        $data['content'] = [];

        $escapedUrl = htmlentities($image->url);
        $escapedName = htmlentities($image->name);
        if ($image->type === 'drawio') {
            $data['content']['html'] = "<div drawio-diagram=\"{$image->id}\"><img src=\"{$escapedUrl}\"></div>";
            $data['content']['markdown'] = $data['content']['html'];
        } else {
            $escapedDisplayThumb = htmlentities($image->thumbs['display']);
            $data['content']['html'] = "<a href=\"{$escapedUrl}\" target=\"_blank\"><img src=\"{$escapedDisplayThumb}\" alt=\"{$escapedName}\"></a>";
            $mdEscapedName = str_replace(']', '', str_replace('[', '', $image->name));
            $mdEscapedThumb = str_replace(']', '', str_replace('[', '', $image->thumbs['display']));
            $data['content']['markdown'] = "![{$mdEscapedName}]({$mdEscapedThumb})";
        }

        return $data;
    }
}
