<?php

namespace BookStack\Http\Controllers;

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

    /**
     * ImageController constructor.
     * @param Image $image
     * @param File  $file
     */
    public function __construct(Image $image, File $file)
    {
        $this->image = $image;
        $this->file = $file;
        parent::__construct();
    }


    /**
     * Get all images, Paginated
     * @param int $page
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll($page = 0)
    {
        $pageSize = 30;
        $images = $this->image->orderBy('created_at', 'desc')
            ->skip($page * $pageSize)->take($pageSize)->get();
        foreach ($images as $image) {
            $this->loadSizes($image);
        }
        $hasMore = $this->image->orderBy('created_at', 'desc')
                ->skip(($page + 1) * $pageSize)->take($pageSize)->count() > 0;
        return response()->json([
            'images' => $images,
            'hasMore' => $hasMore
        ]);
    }

    /**
     * Loads the standard thumbnail sizes for an image.
     * @param Image $image
     */
    private function loadSizes(Image $image)
    {
        $image->thumbnail = $this->getThumbnail($image, 150, 150);
        $image->display = $this->getThumbnail($image, 840, 0, true);
    }

    /**
     * Get the thumbnail for an image.
     * If $keepRatio is true only the width will be used.
     * @param      $image
     * @param int  $width
     * @param int  $height
     * @param bool $keepRatio
     * @return string
     */
    public function getThumbnail($image, $width = 220, $height = 220, $keepRatio = false)
    {
        $explodedPath = explode('/', $image->url);
        $dirPrefix = $keepRatio ? 'scaled-' : 'thumbs-';
        array_splice($explodedPath, 4, 0, [$dirPrefix . $width . '-' . $height]);
        $thumbPath = implode('/', $explodedPath);
        $thumbFilePath = public_path() . $thumbPath;

        // Return the thumbnail url path if already exists
        if (file_exists($thumbFilePath)) {
            return $thumbPath;
        }

        // Otherwise create the thumbnail
        $thumb = ImageTool::make(public_path() . $image->url);
        if($keepRatio) {
            $thumb->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        } else {
            $thumb->fit($width, $height);
        }

        // Create thumbnail folder if it does not exist
        if (!file_exists(dirname($thumbFilePath))) {
            mkdir(dirname($thumbFilePath), 0775, true);
        }

        //Save Thumbnail
        $thumb->save($thumbFilePath);
        return $thumbPath;
    }

    /**
     * Handles image uploads for use on pages.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $this->checkPermission('image-create');
        $this->validate($request, [
            'file' => 'image|mimes:jpeg,gif,png'
        ]);
        $imageUpload = $request->file('file');

        $name = str_replace(' ', '-', $imageUpload->getClientOriginalName());
        $storageName = substr(sha1(time()), 0, 10) . '-' . $name;
        $imagePath = '/uploads/images/' . Date('Y-m-M') . '/';
        $storagePath = public_path() . $imagePath;
        $fullPath = $storagePath . $storageName;
        while (file_exists($fullPath)) {
            $storageName = substr(sha1(rand()), 0, 3) . $storageName;
            $fullPath = $storagePath . $storageName;
        }
        $imageUpload->move($storagePath, $storageName);
        // Create and save image object
        $this->image->name = $name;
        $this->image->url = $imagePath . $storageName;
        $this->image->created_by = auth()->user()->id;
        $this->image->updated_by = auth()->user()->id;
        $this->image->save();
        $this->loadSizes($this->image);
        return response()->json($this->image);
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
        $image = $this->image->findOrFail($imageId);
        $image->fill($request->all());
        $image->save();
        $this->loadSizes($image);
        return response()->json($this->image);
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
        $image = $this->image->findOrFail($id);

        // Check if this image is used on any pages
        $pageSearch = $pageRepo->searchForImage($image->url);
        $isForced = ($request->has('force') && ($request->get('force') === 'true') || $request->get('force') === true);
        if ($pageSearch !== false && !$isForced) {
            return response()->json($pageSearch, 400);
        }

        // Delete files
        $folder = public_path() . dirname($image->url);
        $fileName = basename($image->url);

        // Delete thumbnails
        foreach (glob($folder . '/*') as $file) {
            if (is_dir($file)) {
                $thumbName = $file . '/' . $fileName;
                if (file_exists($file)) {
                    unlink($thumbName);
                }
                // Remove thumb folder if empty
                if (count(glob($file . '/*')) === 0) {
                    rmdir($file);
                }
            }
        }

        // Delete file and database entry
        unlink($folder . '/' . $fileName);
        $image->delete();

        // Delete parent folder if empty
        if (count(glob($folder . '/*')) === 0) {
            rmdir($folder);
        }
        return response()->json('Image Deleted');
    }


}
