<?php

namespace BookStack\Http\Controllers\Images;

use BookStack\Entities\EntityProvider;
use BookStack\Entities\Repos\EntityRepo;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Uploads\ImageRepo;
use Illuminate\Http\Request;
use BookStack\Http\Controllers\Controller;

class CoverImageController extends Controller
{
    protected $imageRepo;
    protected $entityRepo;

    /**
     * CoverImageController constructor.
     * @param ImageRepo $imageRepo
     * @param EntityRepo $entityRepo
     */
    public function __construct(ImageRepo $imageRepo, EntityRepo $entityRepo)
    {
        $this->imageRepo = $imageRepo;
        $this->entityRepo = $entityRepo;

        parent::__construct();
    }

    /**
     * Get a list of cover images, in a list.
     * Can be paged and filtered by entity.
     * @param Request $request
     * @param string $entity
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request, $entity)
    {
        if (!$this->isValidEntityTypeForCover($entity)) {
            return $this->jsonError(trans('errors.image_upload_type_error'));
        }

        $page = $request->get('page', 1);
        $searchTerm = $request->get('search', null);

        $type = 'cover_' . $entity;
        $imgData = $this->imageRepo->getPaginatedByType($type, $page, 24, null, $searchTerm);
        return response()->json($imgData);
    }

    /**
     * Store a new cover image in the system.
     * @param Request $request
     * @param string $entity
     * @return Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function create(Request $request, $entity)
    {
        $this->checkPermission('image-create-all');
        $this->validate($request, [
            'file' => $this->imageRepo->getImageValidationRules(),
            'uploaded_to' => 'required|integer'
        ]);

        if (!$this->isValidEntityTypeForCover($entity)) {
            return $this->jsonError(trans('errors.image_upload_type_error'));
        }

        $uploadedTo = $request->get('uploaded_to', 0);
        $entityInstance = $this->entityRepo->getById($entity, $uploadedTo);
        $this->checkOwnablePermission($entity . '-update', $entityInstance);

        try {
            $type = 'cover_' . $entity;
            $imageUpload = $request->file('file');
            $image = $this->imageRepo->saveNew($imageUpload, $type, $uploadedTo);
        } catch (ImageUploadException $e) {
            return response($e->getMessage(), 500);
        }

        return response()->json($image);
    }

    /**
     * Check if the given entity type is valid entity to have cover images.
     * @param string $entityType
     * @return bool
     */
    protected function isValidEntityTypeForCover(string $entityType)
    {
        return ($entityType === 'book' || $entityType === 'bookshelf');
    }

}
