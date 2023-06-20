<?php

namespace BookStack\Uploads\Controllers;

use BookStack\Entities\Models\Page;
use BookStack\Http\ApiController;
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
                'uploaded_to' => ['required', 'integer'],
                'image' => ['required', 'file', ...$this->getImageValidationRules()],
                'name'  => ['string', 'max:180'],
            ],
            'update' => [
                'name'  => ['string', 'max:180'],
                'image' => ['file', ...$this->getImageValidationRules()],
            ]
        ];
    }

    /**
     * Get a listing of images in the system. Includes gallery (page content) images and drawings.
     * Requires visibility of the page they're originally uploaded to.
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
     *
     * Since "image" is expected to be a file, this needs to be a 'multipart/form-data' type request.
     * The provided "uploaded_to" should be an existing page ID in the system.
     *
     * If the "name" parameter is omitted, the filename of the provided image file will be used instead.
     * The "type" parameter should be 'gallery' for page content images, and 'drawio' should only be used
     * when the file is a PNG file with diagrams.net image data embedded within.
     */
    public function create(Request $request)
    {
        $this->checkPermission('image-create-all');
        $data = $this->validate($request, $this->rules()['create']);
        Page::visible()->findOrFail($data['uploaded_to']);

        $image = $this->imageRepo->saveNew($data['image'], $data['type'], $data['uploaded_to']);

        if (isset($data['name'])) {
            $image->refresh();
            $image->update(['name' => $data['name']]);
        }

        return response()->json($this->formatForSingleResponse($image));
    }

    /**
     * View the details of a single image.
     * The "thumbs" response property contains links to scaled variants that BookStack may use in its UI.
     * The "content" response property provides HTML and Markdown content, in the format that BookStack
     * would typically use by default to add the image in page content, as a convenience.
     * Actual image file data is not provided but can be fetched via the "url" response property.
     */
    public function read(string $id)
    {
        $image = Image::query()->scopes(['visible'])->findOrFail($id);

        return response()->json($this->formatForSingleResponse($image));
    }

    /**
     * Update the details of an existing image in the system.
     * Since "image" is expected to be a file, this needs to be a 'multipart/form-data' type request if providing a
     * new image file. Updated image files should be of the same file type as the original image.
     */
    public function update(Request $request, string $id)
    {
        $data = $this->validate($request, $this->rules()['update']);
        $image = $this->imageRepo->getById($id);
        $this->checkOwnablePermission('page-view', $image->getPage());
        $this->checkOwnablePermission('image-update', $image);

        $this->imageRepo->updateImageDetails($image, $data);
        if (isset($data['image'])) {
            $this->imageRepo->updateImageFile($image, $data['image']);
        }

        return response()->json($this->formatForSingleResponse($image));
    }

    /**
     * Delete an image from the system.
     * Will also delete thumbnails for the image.
     * Does not check or handle image usage so this could leave pages with broken image references.
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
        $data = $image->toArray();
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
