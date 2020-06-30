<?php namespace BookStack\Http\Controllers;

use BookStack\Entities\Repos\PageRepo;
use BookStack\Exceptions\FileUploadException;
use BookStack\Exceptions\NotFoundException;
use BookStack\Uploads\Attachment;
use BookStack\Uploads\AttachmentService;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AttachmentController extends Controller
{
    protected $attachmentService;
    protected $attachment;
    protected $pageRepo;

    /**
     * AttachmentController constructor.
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
     * @throws ValidationException
     * @throws NotFoundException
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
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function uploadUpdate(Request $request, $attachmentId)
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
            return $this->jsonError(trans('errors.attachment_page_mismatch'));
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
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function update(Request $request, $attachmentId)
    {
        $this->validate($request, [
            'uploaded_to' => 'required|integer|exists:pages,id',
            'name' => 'required|string|min:1|max:255',
            'link' =>  'string|min:1|max:255'
        ]);

        $pageId = $request->get('uploaded_to');
        $page = $this->pageRepo->getById($pageId);
        $attachment = $this->attachment->findOrFail($attachmentId);

        $this->checkOwnablePermission('page-update', $page);
        $this->checkOwnablePermission('attachment-create', $attachment);

        if (intval($pageId) !== intval($attachment->uploaded_to)) {
            return $this->jsonError(trans('errors.attachment_page_mismatch'));
        }

        $attachment = $this->attachmentService->updateFile($attachment, $request->all());
        return response()->json($attachment);
    }

    /**
     * Attach a link to a page.
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function attachLink(Request $request)
    {
        $this->validate($request, [
            'uploaded_to' => 'required|integer|exists:pages,id',
            'name' => 'required|string|min:1|max:255',
            'link' =>  'required|string|min:1|max:255'
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
     */
    public function listForPage(int $pageId)
    {
        $page = $this->pageRepo->getById($pageId);
        $this->checkOwnablePermission('page-view', $page);
        return view('pages.attachment-list', [
            'attachments' => $page->attachments->all(),
        ]);
    }

    /**
     * Update the attachment sorting.
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function sortForPage(Request $request, int $pageId)
    {
        $this->validate($request, [
            'order' => 'required|array',
        ]);
        $page = $this->pageRepo->getById($pageId);
        $this->checkOwnablePermission('page-update', $page);

        $attachmentOrder = $request->get('order');
        $this->attachmentService->updateFileOrderWithinPage($attachmentOrder, $pageId);
        return response()->json(['message' => trans('entities.attachments_order_updated')]);
    }

    /**
     * Get an attachment from storage.
     * @throws FileNotFoundException
     * @throws NotFoundException
     */
    public function get(int $attachmentId)
    {
        $attachment = $this->attachment->findOrFail($attachmentId);
        try {
            $page = $this->pageRepo->getById($attachment->uploaded_to);
        } catch (NotFoundException $exception) {
            throw new NotFoundException(trans('errors.attachment_not_found'));
        }

        $this->checkOwnablePermission('page-view', $page);

        if ($attachment->external) {
            return redirect($attachment->path);
        }

        $attachmentContents = $this->attachmentService->getAttachmentFromStorage($attachment);
        return $this->downloadResponse($attachmentContents, $attachment->getFileName());
    }

    /**
     * Delete a specific attachment in the system.
     * @param $attachmentId
     * @return mixed
     * @throws Exception
     */
    public function delete(int $attachmentId)
    {
        $attachment = $this->attachment->findOrFail($attachmentId);
        $this->checkOwnablePermission('attachment-delete', $attachment);
        $this->attachmentService->deleteFile($attachment);
        return response()->json(['message' => trans('entities.attachments_deleted')]);
    }
}
