<?php

namespace BookStack\Uploads\Controllers;

use BookStack\Exceptions\ImageUploadException;
use BookStack\Http\Controller;
use BookStack\Uploads\ImageRepo;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GalleryImageController extends Controller
{
    public function __construct(
        protected ImageRepo $imageRepo
    ) {
    }

    /**
     * Get a list of gallery images, in a list.
     * Can be paged and filtered by entity.
     */
    public function list(Request $request)
    {
        $page = $request->get('page', 1);
        $searchTerm = $request->get('search', null);
        $uploadedToFilter = $request->get('uploaded_to', null);
        $parentTypeFilter = $request->get('filter_type', null);

        $imgData = $this->imageRepo->getEntityFiltered('gallery', $parentTypeFilter, $page, 30, $uploadedToFilter, $searchTerm);

        return view('pages.parts.image-manager-list', [
            'images'  => $imgData['images'],
            'hasMore' => $imgData['has_more'],
        ]);
    }

    /**
     * Store a new gallery image in the system.
     *
     * @throws ValidationException
     */
    public function create(Request $request)
    {
        $this->checkPermission('image-create-all');

        try {
            $this->validate($request, [
                'file' => $this->getImageValidationRules(),
            ]);
        } catch (ValidationException $exception) {
            return $this->jsonError(implode("\n", $exception->errors()['file']));
        }

        try {
            $imageUpload = $request->file('file');
            $uploadedTo = $request->get('uploaded_to', 0);
            $image = $this->imageRepo->saveNew($imageUpload, 'gallery', $uploadedTo);
        } catch (ImageUploadException $e) {
            return response($e->getMessage(), 500);
        }

        return response()->json($image);
    }
}
