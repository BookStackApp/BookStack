<?php

namespace BookStack\Http\Controllers\Images;

use BookStack\Exceptions\ImageUploadException;
use BookStack\Uploads\ImageRepo;
use Illuminate\Http\Request;
use BookStack\Http\Controllers\Controller;

class DrawioImageController extends Controller
{
    protected $imageRepo;

    /**
     * DrawioImageController constructor.
     * @param ImageRepo $imageRepo
     */
    public function __construct(ImageRepo $imageRepo)
    {
        $this->imageRepo = $imageRepo;
        parent::__construct();
    }

    /**
     * Get a list of gallery images, in a list.
     * Can be paged and filtered by entity.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $page = $request->get('page', 1);
        $searchTerm = $request->get('search', null);
        $uploadedToFilter = $request->get('uploaded_to', null);
        $parentTypeFilter = $request->get('filter_type', null);

        $imgData = $this->imageRepo->getEntityFiltered('drawio', $parentTypeFilter, $page, 24, $uploadedToFilter, $searchTerm);
        return response()->json($imgData);
    }

    /**
     * Store a new gallery image in the system.
     * @param Request $request
     * @return Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function create(Request $request)
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
     * Get the content of an image based64 encoded.
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function getAsBase64($id)
    {
        $image = $this->imageRepo->getById($id);
        $page = $image->getPage();
        if ($image === null || $image->type !== 'drawio' || !userCan('page-view', $page)) {
            return $this->jsonError("Image data could not be found");
        }

        $imageData = $this->imageRepo->getImageData($image);
        if ($imageData === null) {
            return $this->jsonError("Image data could not be found");
        }
        return response()->json([
            'content' => base64_encode($imageData)
        ]);
    }
}
