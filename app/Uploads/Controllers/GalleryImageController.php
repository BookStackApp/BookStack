<?php

namespace BookStack\Uploads\Controllers;

use BookStack\Entities\Queries\PageQueries;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Http\Controller;
use BookStack\Permissions\Permission;
use BookStack\Uploads\ImageRepo;
use BookStack\Uploads\ImageResizer;
use BookStack\Util\OutOfMemoryHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GalleryImageController extends Controller
{
    public function __construct(
        protected ImageRepo $imageRepo,
        protected PageQueries $pageQueries,
    ) {
    }

    /**
     * Get a list of gallery images, in a list.
     * Can be paged and filtered by entity.
     */
    public function list(Request $request, ImageResizer $resizer)
    {
        $page = $request->input('page', 1);
        $searchTerm = $request->input('search', null);
        $uploadedToFilter = $request->input('uploaded_to', null);
        $parentTypeFilter = $request->input('filter_type', null);

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
        $this->checkPermission(Permission::ImageCreateAll);

        try {
            $validated = $this->validate($request, [
                'file' => $this->getImageValidationRules(),
                'uploaded_to' => ['required', 'integer'],
            ]);
        } catch (ValidationException $exception) {
            $errors = $exception->errors();
            $messages = array_merge($errors['file'] ?? [], $errors['uploaded_to'] ?? []);
            return $this->jsonError(implode("\n", $messages));
        }

        $uploadedTo = intval($validated['uploaded_to']);
        $targetPage = $this->pageQueries->findVisibleByIdOrFail($uploadedTo);
        $this->checkOwnablePermission(Permission::PageUpdate, $targetPage);

        new OutOfMemoryHandler(function () {
            return $this->jsonError(trans('errors.image_upload_memory_limit'));
        });

        try {
            $imageUpload = $validated['file'];
            $image = $this->imageRepo->saveNew($imageUpload, 'gallery', $uploadedTo);
        } catch (ImageUploadException $e) {
            return response($e->getMessage(), 500);
        }

        return response()->json($image);
    }
}
