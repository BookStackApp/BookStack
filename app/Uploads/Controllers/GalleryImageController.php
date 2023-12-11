<?php

namespace BookStack\Uploads\Controllers;

use BookStack\Exceptions\ImageUploadException;
use BookStack\Http\Controller;
use BookStack\Uploads\ImageRepo;
use BookStack\Uploads\ImageResizer;
use BookStack\Util\OutOfMemoryHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
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
    public function list(Request $request, ImageResizer $resizer)
    {
        $page = $request->get('page', 1);
        $searchTerm = $request->get('search', null);
        $uploadedToFilter = $request->get('uploaded_to', null);
        $parentTypeFilter = $request->get('filter_type', null);

        $imgData = $this->imageRepo->getEntityFiltered('gallery', $parentTypeFilter, $page, 30, $uploadedToFilter, $searchTerm);
        $viewData = [
            'warning' => '',
            'images'  => $imgData['images'],
            'hasMore' => $imgData['has_more'],
        ];

        new OutOfMemoryHandler(function () use ($viewData) {
            $viewData['warning'] = trans('errors.image_gallery_thumbnail_memory_limit');
            return response()->view('pages.parts.image-manager-list', $viewData, 200);
        });

        $resizer->loadGalleryThumbnailsForMany($imgData['images']);

        return view('pages.parts.image-manager-list', $viewData);
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

        new OutOfMemoryHandler(function () {
            return $this->jsonError(trans('errors.image_upload_memory_limit'));
        });

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
