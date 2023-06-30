<?php

namespace BookStack\Uploads\Controllers;

use BookStack\Exceptions\ImageUploadException;
use BookStack\Exceptions\NotFoundException;
use BookStack\Http\Controller;
use BookStack\Uploads\Image;
use BookStack\Uploads\ImageRepo;
use BookStack\Uploads\ImageService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ImageController extends Controller
{
    public function __construct(
        protected ImageRepo $imageRepo,
        protected ImageService $imageService
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
     *
     * @throws ImageUploadException
     * @throws ValidationException
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => ['required', 'min:2', 'string'],
        ]);

        $image = $this->imageRepo->getById($id);
        $this->checkImagePermission($image);
        $this->checkOwnablePermission('image-update', $image);

        $image = $this->imageRepo->updateImageDetails($image, $request->all());

        $this->imageRepo->loadThumbs($image);

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

        $this->imageRepo->loadThumbs($image);

        return view('pages.parts.image-manager-form', [
            'image'          => $image,
            'dependantPages' => $dependantPages ?? null,
        ]);
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
     * Check related page permission and ensure type is drawio or gallery.
     */
    protected function checkImagePermission(Image $image)
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
