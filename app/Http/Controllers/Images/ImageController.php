<?php namespace BookStack\Http\Controllers\Images;

use BookStack\Entities\Repos\EntityRepo;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Http\Controllers\Controller;
use BookStack\Repos\PageRepo;
use BookStack\Uploads\Image;
use BookStack\Uploads\ImageRepo;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    protected $image;
    protected $file;
    protected $imageRepo;

    /**
     * ImageController constructor.
     * @param Image $image
     * @param File $file
     * @param ImageRepo $imageRepo
     */
    public function __construct(Image $image, File $file, ImageRepo $imageRepo)
    {
        $this->image = $image;
        $this->file = $file;
        $this->imageRepo = $imageRepo;
        parent::__construct();
    }

    /**
     * Provide an image file from storage.
     * @param string $path
     * @return mixed
     */
    public function showImage(string $path)
    {
        $path = storage_path('uploads/images/' . $path);
        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }


    /**
     * Update image details
     * @param integer $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ImageUploadException
     * @throws \Exception
     */
    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:2|string'
        ]);

        $image = $this->imageRepo->getById($id);
        $this->checkImagePermission($image);
        $this->checkOwnablePermission('image-update', $image);

        $image = $this->imageRepo->updateImageDetails($image, $request->all());
        return response()->json($image);
    }

    /**
     * Show the usage of an image on pages.
     * @param \BookStack\Entities\Repos\EntityRepo $entityRepo
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function usage(EntityRepo $entityRepo, $id)
    {
        $image = $this->imageRepo->getById($id);
        $this->checkImagePermission($image);
        $pageSearch = $entityRepo->searchForImage($image->url);
        return response()->json($pageSearch);
    }

    /**
     * Deletes an image and all thumbnail/image files
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $image = $this->imageRepo->getById($id);
        $this->checkOwnablePermission('image-delete', $image);
        $this->checkImagePermission($image);

        $this->imageRepo->destroyImage($image);
        return response()->json(trans('components.images_deleted'));
    }

    /**
     * Check related page permission and ensure type is drawio or gallery.
     * @param Image $image
     */
    protected function checkImagePermission(Image $image)
    {
        if ($image->type !== 'drawio' || $image->type !== 'gallery') {
            $this->showPermissionError();
        }

        $relatedPage = $image->getPage();
        if ($relatedPage) {
            $this->checkOwnablePermission('page-view', $relatedPage);
        }
    }
}
