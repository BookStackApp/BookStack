<?php

namespace BookStack\Http\Controllers;

use BookStack\Repos\ImageRepo;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image as ImageTool;
use Illuminate\Support\Facades\DB;
use BookStack\Image;
use BookStack\Repos\PageRepo;

class ImageController extends Controller
{
    protected $image;
    protected $file;
    protected $imageRepo;

    /**
     * ImageController constructor.
     * @param Image     $image
     * @param File      $file
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
     * Get all images, Paginated
     * @param int $page
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllGallery($page = 0)
    {
        $imgData = $this->imageRepo->getAllGallery($page);
        return response()->json($imgData);
    }


    /**
     * Handles image uploads for use on pages.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadGallery(Request $request)
    {
        $this->checkPermission('image-create');
        $this->validate($request, [
            'file' => 'image|mimes:jpeg,gif,png'
        ]);

        $imageUpload = $request->file('file');
        $image = $this->imageRepo->saveNew($imageUpload, 'gallery');
        return response()->json($image);
    }


    /**
     * Update image details
     * @param         $imageId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($imageId, Request $request)
    {
        $this->checkPermission('image-update');
        $this->validate($request, [
            'name' => 'required|min:2|string'
        ]);
        $image = $this->imageRepo->getById($imageId);
        $image = $this->imageRepo->updateImageDetails($image, $request->all());
        return response()->json($image);
    }


    /**
     * Deletes an image and all thumbnail/image files
     * @param PageRepo $pageRepo
     * @param Request  $request
     * @param int      $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(PageRepo $pageRepo, Request $request, $id)
    {
        $this->checkPermission('image-delete');
        $image = $this->imageRepo->getById($id);

        // Check if this image is used on any pages
        $isForced = ($request->has('force') && ($request->get('force') === 'true') || $request->get('force') === true);
        if (!$isForced) {
            $pageSearch = $pageRepo->searchForImage($image->url);
            if ($pageSearch !== false) {
                return response()->json($pageSearch, 400);
            }
        }

        $this->imageRepo->destroyImage($image);
        return response()->json('Image Deleted');
    }


}
