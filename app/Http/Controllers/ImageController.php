<?php namespace BookStack\Http\Controllers;

use BookStack\Exceptions\ImageUploadException;
use BookStack\Exceptions\NotFoundException;
use BookStack\Repos\EntityRepo;
use BookStack\Repos\ImageRepo;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Http\Request;
use BookStack\Image;
use BookStack\Repos\PageRepo;

class ImageController extends Controller
{
    protected $image;
    protected $file;
    protected $imageRepo;

    /**
     * ImageController constructor.
     * @param Image $image
     * @param File $file
     * @param ImageRepo $imageRepo
     */
    public function __construct(Image $image, File $file, ImageRepo $imageRepo)
    {
        $this->image = $image;
        $this->file = $file;
        $this->imageRepo = $imageRepo;
        parent::__construct();
    }

    /**
     * Provide an image file from storage.
     * @param string $path
     * @return mixed
     */
    public function showImage(string $path)
    {
        $path = storage_path('uploads/images/' . $path);
        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    /**
     * Get all images for a specific type, Paginated
     * @param string $type
     * @param int $page
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllByType($type, $page = 0)
    {
        $imgData = $this->imageRepo->getPaginatedByType($type, $page);
        return response()->json($imgData);
    }

    /**
     * Search through images within a particular type.
     * @param $type
     * @param int $page
     * @param Request $request
     * @return mixed
     */
    public function searchByType(Request $request, $type, $page = 0)
    {
        $this->validate($request, [
            'term' => 'required|string'
        ]);

        $searchTerm = $request->get('term');
        $imgData = $this->imageRepo->searchPaginatedByType($type, $searchTerm, $page, 24);
        return response()->json($imgData);
    }

    /**
     * Get all images for a user.
     * @param int $page
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllForUserType($page = 0)
    {
        $imgData = $this->imageRepo->getPaginatedByType('user', $page, 24, $this->currentUser->id);
        return response()->json($imgData);
    }

    /**
     * Get gallery images with a specific filter such as book or page
     * @param $filter
     * @param int $page
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function getGalleryFiltered(Request $request, $filter, $page = 0)
    {
        $this->validate($request, [
            'page_id' => 'required|integer'
        ]);

        $validFilters = collect(['page', 'book']);
        if (!$validFilters->contains($filter)) {
            return response('Invalid filter', 500);
        }

        $pageId = $request->get('page_id');
        $imgData = $this->imageRepo->getGalleryFiltered(strtolower($filter), $pageId, $page, 24);

        return response()->json($imgData);
    }

    /**
     * Handles image uploads for use on pages.
     * @param string $type
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function uploadByType($type, Request $request)
    {
        $this->checkPermission('image-create-all');
        $this->validate($request, [
            'file' => 'is_image'
        ]);

        if (!$this->imageRepo->isValidType($type)) {
            return $this->jsonError(trans('errors.image_upload_type_error'));
        }

        $imageUpload = $request->file('file');

        try {
            $uploadedTo = $request->get('uploaded_to', 0);
            $image = $this->imageRepo->saveNew($imageUpload, $type, $uploadedTo);
        } catch (ImageUploadException $e) {
            return response($e->getMessage(), 500);
        }


        return response()->json($image);
    }

    /**
     * Upload a drawing to the system.
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function uploadDrawing(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|string',
            'uploaded_to' => 'required|integer'
        ]);
        $this->checkPermission('image-create-all');
        $imageBase64Data = $request->get('image');

        try {
            $uploadedTo = $request->get('uploaded_to', 0);
            $image = $this->imageRepo->saveDrawing($imageBase64Data, $uploadedTo);
        } catch (ImageUploadException $e) {
            return response($e->getMessage(), 500);
        }

        return response()->json($image);
    }

    /**
     * Replace the data content of a drawing.
     * @param string $id
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function replaceDrawing(string $id, Request $request)
    {
        $this->validate($request, [
            'image' => 'required|string'
        ]);
        $this->checkPermission('image-create-all');

        $imageBase64Data = $request->get('image');
        $image = $this->imageRepo->getById($id);
        $this->checkOwnablePermission('image-update', $image);

        try {
            $image = $this->imageRepo->replaceDrawingContent($image, $imageBase64Data);
        } catch (ImageUploadException $e) {
            return response($e->getMessage(), 500);
        }

        return response()->json($image);
    }

    /**
     * Get the content of an image based64 encoded.
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function getBase64Image($id)
    {
        $image = $this->imageRepo->getById($id);
        $imageData = $this->imageRepo->getImageData($image);
        if ($imageData === null) {
            return $this->jsonError("Image data could not be found");
        }
        return response()->json([
            'content' => base64_encode($imageData)
        ]);
    }

    /**
     * Generate a sized thumbnail for an image.
     * @param $id
     * @param $width
     * @param $height
     * @param $crop
     * @return \Illuminate\Http\JsonResponse
     * @throws ImageUploadException
     * @throws \Exception
     */
    public function getThumbnail($id, $width, $height, $crop)
    {
        $this->checkPermission('image-create-all');
        $image = $this->imageRepo->getById($id);
        $thumbnailUrl = $this->imageRepo->getThumbnail($image, $width, $height, $crop == 'false');
        return response()->json(['url' => $thumbnailUrl]);
    }

    /**
     * Update image details
     * @param integer $imageId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ImageUploadException
     * @throws \Exception
     */
    public function update($imageId, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:2|string'
        ]);
        $image = $this->imageRepo->getById($imageId);
        $this->checkOwnablePermission('image-update', $image);
        $image = $this->imageRepo->updateImageDetails($image, $request->all());
        return response()->json($image);
    }

    /**
     * Deletes an image and all thumbnail/image files
     * @param EntityRepo $entityRepo
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(EntityRepo $entityRepo, Request $request, $id)
    {
        $image = $this->imageRepo->getById($id);
        $this->checkOwnablePermission('image-delete', $image);

        // Check if this image is used on any pages
        $isForced = in_array($request->get('force', ''), [true, 'true']);
        if (!$isForced) {
            $pageSearch = $entityRepo->searchForImage($image->url);
            if ($pageSearch !== false) {
                return response()->json($pageSearch, 400);
            }
        }

        $this->imageRepo->destroyImage($image);
        return response()->json(trans('components.images_deleted'));
    }
}
