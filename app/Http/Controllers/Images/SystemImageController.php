<?php

namespace BookStack\Http\Controllers\Images;

use BookStack\Exceptions\ImageUploadException;
use BookStack\Uploads\ImageRepo;
use Illuminate\Http\Request;
use BookStack\Http\Controllers\Controller;

class SystemImageController extends Controller
{
    protected $imageRepo;

    /**
     * SystemImageController constructor.
     * @param ImageRepo $imageRepo
     */
    public function __construct(ImageRepo $imageRepo)
    {
        $this->imageRepo = $imageRepo;
        parent::__construct();
    }

    /**
     * Get a list of system images, in a list.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $this->checkPermission('settings-manage');
        $page = $request->get('page', 1);
        $searchTerm = $request->get('search', null);

        $imgData = $this->imageRepo->getPaginatedByType('system', $page, 24, null, $searchTerm);
        return response()->json($imgData);
    }

    /**
     * Store a new system image.
     * @param Request $request
     * @return Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $this->checkPermission('image-create-all');
        $this->checkPermission('settings-manage');

        $this->validate($request, [
            'file' => $this->imageRepo->getImageValidationRules()
        ]);

        try {
            $imageUpload = $request->file('file');
            $image = $this->imageRepo->saveNew($imageUpload, 'system', 0);
        } catch (ImageUploadException $e) {
            return response($e->getMessage(), 500);
        }

        return response()->json($image);
    }

}
