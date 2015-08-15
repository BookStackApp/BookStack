<?php

namespace Oxbow\Http\Controllers;

use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image as ImageTool;
use Illuminate\Support\Facades\DB;
use Oxbow\Http\Requests;
use Oxbow\Image;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class ImageController extends Controller
{
    protected $image;
    protected $file;

    /**
     * ImageController constructor.
     * @param Image $image
     * @param File $file
     */
    public function __construct(Image $image, File $file)
    {
        $this->image = $image;
        $this->file = $file;
    }

    /**
     * Returns an image from behind the public-facing application.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getImage(Request $request)
    {
        $cacheTime = 60*60*24;
        $path = storage_path() . '/' . $request->path();
        $modifiedTime = $this->file->lastModified($path);
        $eTag = md5($modifiedTime . $path);
        $headerLastModified = gmdate('r', $modifiedTime);
        $headerExpires = gmdate('r', $modifiedTime + $cacheTime);

        $headers = [
            'Last-Modified' => $headerLastModified,
            'Cache-Control' => 'must-revalidate',
            'Pragma' => 'public',
            'Expires' => $headerExpires,
            'Etag' => $eTag
        ];

        $browserModifiedSince = $request->header('If-Modified-Since');
        $browserNoneMatch = $request->header('If-None-Match');
        if($browserModifiedSince !== null && file_exists($path) && ($browserModifiedSince == $headerLastModified || $browserNoneMatch == $eTag)) {
            return response()->make('', 304, $headers);
        }

        if(file_exists($path)) {
            return response()->make(file_get_contents($path), 200, array_merge($headers, [
                'Content-Type' => $this->file->mimeType($path),
                'Content-Length' => filesize($path),
            ]));
        }
        abort(404);
    }

    /**
     * Get all images, Paginated
     * @param int $page
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll($page = 0)
    {
        $pageSize = 30;
        $images = DB::table('images')->orderBy('created_at', 'desc')
            ->skip($page*$pageSize)->take($pageSize)->get();
        foreach($images as $image) {
            $image->thumbnail = $this->getThumbnail($image, 150, 150);
        }
        $hasMore = count(DB::table('images')->orderBy('created_at', 'desc')
            ->skip(($page+1)*$pageSize)->take($pageSize)->get()) > 0;
        return response()->json([
            'images' => $images,
            'hasMore' => $hasMore
        ]);
    }

    /**
     * Get the thumbnail for an image.
     * @param $image
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getThumbnail($image, $width = 220, $height = 220)
    {
        $explodedPath = explode('/', $image->url);
        array_splice($explodedPath, 4, 0, ['thumbs-' . $width . '-' . $height]);
        $thumbPath = implode('/', $explodedPath);
        $thumbFilePath = public_path() . $thumbPath;

        // Return the thumbnail url path if already exists
        if(file_exists($thumbFilePath)) {
            return $thumbPath;
        }

        // Otherwise create the thumbnail
        $thumb = ImageTool::make(public_path() . $image->url);
        $thumb->fit($width, $height);

        // Create thumbnail folder if it does not exist
        if(!file_exists(dirname($thumbFilePath))) {
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
        $imageUpload = $request->file('file');
        $name = str_replace(' ', '-', $imageUpload->getClientOriginalName());
        $storageName = substr(sha1(time()), 0, 10) . '-' . $name;
        $imagePath = '/uploads/images/'.Date('Y-m-M').'/';
        $storagePath = public_path(). $imagePath;
        $fullPath = $storagePath . $storageName;
        while(file_exists($fullPath)) {
            $storageName = substr(sha1(rand()), 0, 3) . $storageName;
            $fullPath = $storagePath . $storageName;
        }
        $imageUpload->move($storagePath, $storageName);
        // Create and save image object
        $this->image->name = $name;
        $this->image->url = $imagePath . $storageName;
        $this->image->created_by = Auth::user()->id;
        $this->image->updated_by = Auth::user()->id;
        $this->image->save();
        $this->image->thumbnail = $this->getThumbnail($this->image, 150, 150);
        return response()->json($this->image);
    }

    /**
     * Update image details
     * @param $imageId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($imageId, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:2|string'
        ]);
        $image = $this->image->findOrFail($imageId);
        $image->fill($request->all());
        $image->save();
        return response()->json($this->image);
    }

    /**
     * Deletes an image and all thumbnail/image files
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $image = $this->image->findOrFail($id);

        // Delete files
        $folder = public_path() . dirname($image->url);
        $pattern = '/' . preg_quote(basename($image->url)). '/';
        $dir = new RecursiveDirectoryIterator($folder);
        $ite = new RecursiveIteratorIterator($dir);
        $files = new RegexIterator($ite, $pattern, RegexIterator::ALL_MATCHES);
        foreach($files as $path => $file) {
            unlink($path);
        }
        $image->delete();
        return response()->json('Image Deleted');
    }


}
