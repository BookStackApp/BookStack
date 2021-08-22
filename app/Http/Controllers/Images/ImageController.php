<?php

namespace BookStack\Http\Controllers\Images;

use BookStack\Exceptions\ImageUploadException;
use BookStack\Exceptions\NotFoundException;
use BookStack\Http\Controllers\Controller;
use BookStack\Uploads\Image;
use BookStack\Uploads\ImageRepo;
use Exception;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ImageController extends Controller
{
    protected $image;
    protected $file;
    protected $imageRepo;

    /**
     * ImageController constructor.
     */
    public function __construct(Image $image, File $file, ImageRepo $imageRepo)
    {
        $this->image = $image;
        $this->file = $file;
        $this->imageRepo = $imageRepo;
    }

    /**
     * Provide an image file from storage.
     *
     * @throws NotFoundException
     */
    public function showImage(string $path)
    {
        $path = storage_path('uploads/images/' . $path);
        if (!file_exists($path)) {
            throw (new NotFoundException(trans('errors.image_not_found')))
                ->setSubtitle(trans('errors.image_not_found_subtitle'))
                ->setDetails(trans('errors.image_not_found_details'));
        }

        return response()->file($path);
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
            'name' => 'required|min:2|string',
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
