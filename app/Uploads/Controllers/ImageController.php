<?php

namespace BookStack\Uploads\Controllers;

use BookStack\Exceptions\ImageUploadException;
use BookStack\Exceptions\NotFoundException;
use BookStack\Exceptions\NotifyException;
use BookStack\Http\Controller;
use BookStack\Uploads\Image;
use BookStack\Uploads\ImageRepo;
use BookStack\Uploads\ImageResizer;
use BookStack\Uploads\ImageService;
use BookStack\Util\OutOfMemoryHandler;
use Exception;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function __construct(
        protected ImageRepo $imageRepo,
        protected ImageService $imageService,
        protected ImageResizer $imageResizer,
    ) {
    }

    /**
     * Provide an image file from storage.
     *
     * @throws NotFoundException
     */
    public function showImage(string $path)
    {
        if (!$this->imageService->pathAccessibleInLocalSecure($path)) {
            throw (new NotFoundException(trans('errors.image_not_found')))
                ->setSubtitle(trans('errors.image_not_found_subtitle'))
                ->setDetails(trans('errors.image_not_found_details'));
        }

        return $this->imageService->streamImageFromStorageResponse('gallery', $path);
    }

    /**
     * Update image details.
     */
    public function update(Request $request, string $id)
    {
        $data = $this->validate($request, [
            'name' => ['required', 'min:2', 'string'],
        ]);

        $image = $this->imageRepo->getById($id);
        $this->checkImagePermission($image);
        $this->checkOwnablePermission('image-update', $image);

        $image = $this->imageRepo->updateImageDetails($image, $data);

        return view('pages.parts.image-manager-form', [
            'image'          => $image,
            'dependantPages' => null,
        ]);
    }

    /**
     * Update the file for an existing image.
     */
    public function updateFile(Request $request, string $id)
    {
        $this->validate($request, [
            'file' => ['required', 'file', ...$this->getImageValidationRules()],
        ]);

        $image = $this->imageRepo->getById($id);
        $this->checkImagePermission($image);
        $this->checkOwnablePermission('image-update', $image);
        $file = $request->file('file');

        new OutOfMemoryHandler(function () {
            return $this->jsonError(trans('errors.image_upload_memory_limit'));
        });

        try {
            $this->imageRepo->updateImageFile($image, $file);
        } catch (ImageUploadException $exception) {
            return $this->jsonError($exception->getMessage());
        }

        return response('');
    }

    /**
     * Get the form for editing the given image.
     *
     * @throws Exception
     */
    public function edit(Request $request, string $id)
    {
        $image = $this->imageRepo->getById($id);
        $this->checkImagePermission($image);

        if ($request->has('delete')) {
            $dependantPages = $this->imageRepo->getPagesUsingImage($image);
        }

        $viewData = [
            'image'          => $image,
            'dependantPages' => $dependantPages ?? null,
            'warning'        => '',
        ];

        new OutOfMemoryHandler(function () use ($viewData) {
            $viewData['warning'] = trans('errors.image_thumbnail_memory_limit');
            return response()->view('pages.parts.image-manager-form', $viewData);
        });

        $this->imageResizer->loadGalleryThumbnailsForImage($image, false);

        return view('pages.parts.image-manager-form', $viewData);
    }

    /**
     * Deletes an image and all thumbnail/image files.
     *
     * @throws Exception
     */
    public function destroy(string $id)
    {
        $image = $this->imageRepo->getById($id);
        $this->checkOwnablePermission('image-delete', $image);
        $this->checkImagePermission($image);

        $this->imageRepo->destroyImage($image);

        return response('');
    }

    /**
     * Rebuild the thumbnails for the given image.
     */
    public function rebuildThumbnails(string $id)
    {
        $image = $this->imageRepo->getById($id);
        $this->checkImagePermission($image);
        $this->checkOwnablePermission('image-update', $image);

        new OutOfMemoryHandler(function () {
            return $this->jsonError(trans('errors.image_thumbnail_memory_limit'));
        });

        $this->imageResizer->loadGalleryThumbnailsForImage($image, true);

        return response(trans('components.image_rebuild_thumbs_success'));
    }

    /**
     * Check related page permission and ensure type is drawio or gallery.
     * @throws NotifyException
     */
    protected function checkImagePermission(Image $image): void
    {
        if ($image->type !== 'drawio' && $image->type !== 'gallery') {
            $this->showPermissionError();
        }

        $relatedPage = $image->getPage();
        if ($relatedPage) {
            $this->checkOwnablePermission('page-view', $relatedPage);
        }
    }
}
