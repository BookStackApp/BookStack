<?php namespace BookStack\Http\Controllers;

use BookStack\Exceptions\FileUploadException;
use BookStack\Attachment;
use BookStack\Repos\PageRepo;
use BookStack\Services\AttachmentService;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    protected $attachmentService;
    protected $attachment;
    protected $pageRepo;

    /**
     * AttachmentController constructor.
     * @param AttachmentService $attachmentService
     * @param Attachment $attachment
     * @param PageRepo $pageRepo
     */
    public function __construct(AttachmentService $attachmentService, Attachment $attachment, PageRepo $pageRepo)
    {
        $this->attachmentService = $attachmentService;
        $this->attachment = $attachment;
        $this->pageRepo = $pageRepo;
        parent::__construct();
    }


    /**
     * Endpoint at which attachments are uploaded to.
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function upload(Request $request)
    {
        $this->validate($request, [
            'uploaded_to' => 'required|integer|exists:pages,id',
            'file' => 'required|file'
        ]);

        $pageId = $request->get('uploaded_to');
        $page = $this->pageRepo->getById($pageId);

        $this->checkPermission('attachment-create-all');
        $this->checkOwnablePermission('page-update', $page);

        $uploadedFile = $request->file('file');

        try {
            $attachment = $this->attachmentService->saveNewUpload($uploadedFile, $pageId);
        } catch (FileUploadException $e) {
            return response($e->getMessage(), 500);
        }

        return response()->json($attachment);
    }

    /**
     * Update an uploaded attachment.
     * @param int $attachmentId
     * @param Request $request
     * @return mixed
     */
    public function uploadUpdate($attachmentId, Request $request)
    {
        $this->validate($request, [
            'uploaded_to' => 'required|integer|exists:pages,id',
            'file' => 'required|file'
        ]);

        $pageId = $request->get('uploaded_to');
        $page = $this->pageRepo->getById($pageId);
        $attachment = $this->attachment->findOrFail($attachmentId);

        $this->checkOwnablePermission('page-update', $page);
        $this->checkOwnablePermission('attachment-create', $attachment);
        
        if (intval($pageId) !== intval($attachment->uploaded_to)) {
            return $this->jsonError('Page mismatch during attached file update');
        }

        $uploadedFile = $request->file('file');

        try {
            $attachment = $this->attachmentService->saveUpdatedUpload($uploadedFile, $attachment);
        } catch (FileUploadException $e) {
            return response($e->getMessage(), 500);
        }

        return response()->json($attachment);
    }

    /**
     * Update the details of an existing file.
     * @param $attachmentId
     * @param Request $request
     * @return Attachment|mixed
     */
    public function update($attachmentId, Request $request)
    {
        $this->validate($request, [
            'uploaded_to' => 'required|integer|exists:pages,id',
            'name' => 'required|string|min:1|max:255',
            'link' =>  'url|min:1|max:255'
        ]);

        $pageId = $request->get('uploaded_to');
        $page = $this->pageRepo->getById($pageId);
        $attachment = $this->attachment->findOrFail($attachmentId);

        $this->checkOwnablePermission('page-update', $page);
        $this->checkOwnablePermission('attachment-create', $attachment);

        if (intval($pageId) !== intval($attachment->uploaded_to)) {
            return $this->jsonError('Page mismatch during attachment update');
        }

        $attachment = $this->attachmentService->updateFile($attachment, $request->all());
        return $attachment;
    }

    /**
     * Attach a link to a page.
     * @param Request $request
     * @return mixed
     */
    public function attachLink(Request $request)
    {
        $this->validate($request, [
            'uploaded_to' => 'required|integer|exists:pages,id',
            'name' => 'required|string|min:1|max:255',
            'link' =>  'required|url|min:1|max:255'
        ]);

        $pageId = $request->get('uploaded_to');
        $page = $this->pageRepo->getById($pageId);

        $this->checkPermission('attachment-create-all');
        $this->checkOwnablePermission('page-update', $page);

        $attachmentName = $request->get('name');
        $link = $request->get('link');
        $attachment = $this->attachmentService->saveNewFromLink($attachmentName, $link, $pageId);

        return response()->json($attachment);
    }

    /**
     * Get the attachments for a specific page.
     * @param $pageId
     * @return mixed
     */
    public function listForPage($pageId)
    {
        $page = $this->pageRepo->getById($pageId);
        $this->checkOwnablePermission('page-view', $page);
        return response()->json($page->files);
    }

    /**
     * Update the attachment sorting.
     * @param $pageId
     * @param Request $request
     * @return mixed
     */
    public function sortForPage($pageId, Request $request)
    {
        $this->validate($request, [
            'files' => 'required|array',
            'files.*.id' => 'required|integer',
        ]);
        $page = $this->pageRepo->getById($pageId);
        $this->checkOwnablePermission('page-update', $page);

        $attachments = $request->get('files');
        $this->attachmentService->updateFileOrderWithinPage($attachments, $pageId);
        return response()->json(['message' => 'Attachment order updated']);
    }

    /**
     * Get an attachment from storage.
     * @param $attachmentId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Symfony\Component\HttpFoundation\Response
     */
    public function get($attachmentId)
    {
        $attachment = $this->attachment->findOrFail($attachmentId);
        $page = $this->pageRepo->getById($attachment->uploaded_to);
        $this->checkOwnablePermission('page-view', $page);

        if ($attachment->external) {
            return redirect($attachment->path);
        }

        $attachmentContents = $this->attachmentService->getAttachmentFromStorage($attachment);
        return response($attachmentContents, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'. $attachment->getFileName() .'"'
        ]);
    }

    /**
     * Delete a specific attachment in the system.
     * @param $attachmentId
     * @return mixed
     */
    public function delete($attachmentId)
    {
        $attachment = $this->attachment->findOrFail($attachmentId);
        $this->checkOwnablePermission('attachment-delete', $attachment);
        $this->attachmentService->deleteFile($attachment);
        return response()->json(['message' => 'Attachment deleted']);
    }
}
