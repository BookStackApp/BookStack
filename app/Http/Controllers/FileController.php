<?php

namespace BookStack\Http\Controllers;

use BookStack\Exceptions\FileUploadException;
use BookStack\File;
use BookStack\Page;
use BookStack\Repos\PageRepo;
use BookStack\Services\FileService;
use Illuminate\Http\Request;

use BookStack\Http\Requests;

class FileController extends Controller
{
    protected $fileService;
    protected $file;
    protected $pageRepo;

    /**
     * FileController constructor.
     * @param FileService $fileService
     * @param File $file
     * @param PageRepo $pageRepo
     */
    public function __construct(FileService $fileService, File $file, PageRepo $pageRepo)
    {
        $this->fileService = $fileService;
        $this->file = $file;
        $this->pageRepo = $pageRepo;
    }


    /**
     * Endpoint at which files are uploaded to.
     * @param Request $request
     */
    public function upload(Request $request)
    {
        // TODO - Add file upload permission check
        // TODO - ensure user has permission to edit relevant page.
        // TODO - ensure uploads are deleted on page delete.

        $this->validate($request, [
            'uploaded_to' => 'required|integer|exists:pages,id'
        ]);

        $uploadedFile = $request->file('file');
        $pageId = $request->get('uploaded_to');

        try {
            $file = $this->fileService->saveNewUpload($uploadedFile, $pageId);
        } catch (FileUploadException $e) {
            return response($e->getMessage(), 500);
        }

        return response()->json($file);
    }

    /**
     * Get the files for a specific page.
     * @param $pageId
     * @return mixed
     */
    public function getFilesForPage($pageId)
    {
        // TODO - check view permission on page?
        $page = $this->pageRepo->getById($pageId);
        return response()->json($page->files);
    }

    /**
     * Update the file sorting.
     * @param $pageId
     * @param Request $request
     * @return mixed
     */
    public function sortFilesForPage($pageId, Request $request)
    {
        $this->validate($request, [
            'files' => 'required|array',
            'files.*.id' => 'required|integer',
        ]);
        $page = $this->pageRepo->getById($pageId);
        $files = $request->get('files');
        $this->fileService->updateFileOrderWithinPage($files, $pageId);
        return response()->json(['message' => 'File order updated']);
    }


}
