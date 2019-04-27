<?php

namespace BookStack\Http\Controllers\Images;

use BookStack\Exceptions\ImageUploadException;
use BookStack\Uploads\ImageRepo;
use Illuminate\Http\Request;
use BookStack\Http\Controllers\Controller;

class UserImageController extends Controller
{
    protected $imageRepo;

    /**
     * UserImageController constructor.
     * @param ImageRepo $imageRepo
     */
    public function __construct(ImageRepo $imageRepo)
    {
        $this->imageRepo = $imageRepo;
        parent::__construct();
    }

    /**
     * Get a list of user profile images, in a list.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $page = $request->get('page', 1);
        $searchTerm = $request->get('search', null);
        $userId = $request->get('uploaded_to', null);

        $this->checkPermissionOrCurrentUser('users-manage', $userId);

        $imgData = $this->imageRepo->getPaginatedByType('user', $page, 24, $userId, $searchTerm);
        return response()->json($imgData);
    }

    /**
     * Store a new user profile image in the system.
     * @param Request $request
     * @return Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $this->checkPermission('image-create-all');

        $this->validate($request, [
            'uploaded_to' => 'required|integer',
            'file' => $this->imageRepo->getImageValidationRules()
        ]);

        $userId = $request->get('uploaded_to', null);
        $this->checkPermissionOrCurrentUser('users-manage', $userId);

        try {
            $imageUpload = $request->file('file');
            $uploadedTo = $request->get('uploaded_to', 0);
            $image = $this->imageRepo->saveNew($imageUpload, 'user', $uploadedTo);
        } catch (ImageUploadException $e) {
            return response($e->getMessage(), 500);
        }

        return response()->json($image);
    }

}
