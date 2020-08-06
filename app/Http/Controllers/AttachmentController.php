<?php namespace BookStack\Http\Controllers;

use BookStack\Entities\Repos\PageRepo;
use BookStack\Exceptions\FileUploadException;
use BookStack\Exceptions\NotFoundException;
use BookStack\Uploads\Attachment;
use BookStack\Uploads\AttachmentService;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
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
     */
    public function uploadUpdate(Request $request, $attachmentId)
    {
        $this->validate($request, [
            'file' => 'required|file'
        ]);

        $attachment = $this->attachment->newQuery()->findOrFail($attachmentId);
        $this->checkOwnablePermission('view', $attachment->page);
        $this->checkOwnablePermission('page-update', $attachment->page);
        $this->checkOwnablePermission('attachment-create', $attachment);

        $uploadedFile = $request->file('file');

        try {
            $attachment = $this->attachmentService->saveUpdatedUpload($uploadedFile, $attachment);
        } catch (FileUploadException $e) {
            return response($e->getMessage(), 500);
        }

        return response()->json($attachment);
    }

    /**
     * Get the update form for an attachment.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getUpdateForm(string $attachmentId)
    {
        $attachment = $this->attachment->findOrFail($attachmentId);

        $this->checkOwnablePermission('page-update', $attachment->page);
        $this->checkOwnablePermission('attachment-create', $attachment);

        return view('attachments.manager-edit-form', [
            'attachment' => $attachment,
        ]);
    }

    /**
     * Update the details of an existing file.
     */
    public function update(Request $request, string $attachmentId)
    {
        $attachment = $this->attachment->newQuery()->findOrFail($attachmentId);

        try {
            $this->validate($request, [
                'attachment_edit_name' => 'required|string|min:1|max:255',
                'attachment_edit_url' =>  'string|min:1|max:255'
            ]);
        } catch (ValidationException $exception) {
            return response()->view('attachments.manager-edit-form', array_merge($request->only(['attachment_edit_name', 'attachment_edit_url']), [
                'attachment' => $attachment,
                'errors' => new MessageBag($exception->errors()),
            ]), 422);
        }

        $this->checkOwnablePermission('view', $attachment->page);
        $this->checkOwnablePermission('page-update', $attachment->page);
        $this->checkOwnablePermission('attachment-create', $attachment);

        $attachment = $this->attachmentService->updateFile($attachment, [
            'name' => $request->get('attachment_edit_name'),
            'link' => $request->get('attachment_edit_url'),
        ]);

        return view('attachments.manager-edit-form', [
            'attachment' => $attachment,
        ]);
    }

    /**
     * Attach a link to a page.
     * @throws NotFoundException
     */
    public function attachLink(Request $request)
    {
        $pageId = $request->get('attachment_link_uploaded_to');

        try {
            $this->validate($request, [
                'attachment_link_uploaded_to' => 'required|integer|exists:pages,id',
                'attachment_link_name' => 'required|string|min:1|max:255',
                'attachment_link_url' =>  'required|string|min:1|max:255'
            ]);
        } catch (ValidationException $exception) {
            return response()->view('attachments.manager-link-form', array_merge($request->only(['attachment_link_name', 'attachment_link_url']), [
                'pageId' => $pageId,
                'errors' => new MessageBag($exception->errors()),
            ]), 422);
        }

        $page = $this->pageRepo->getById($pageId);

        $this->checkPermission('attachment-create-all');
        $this->checkOwnablePermission('page-update', $page);

        $attachmentName = $request->get('attachment_link_name');
        $link = $request->get('attachment_link_url');
        $attachment = $this->attachmentService->saveNewFromLink($attachmentName, $link, $pageId);

        return view('attachments.manager-link-form', [
            'pageId' => $pageId,
        ]);
    }

    /**
     * Get the attachments for a specific page.
     */
    public function listForPage(int $pageId)
    {
        $page = $this->pageRepo->getById($pageId);
        $this->checkOwnablePermission('page-view', $page);
        return view('attachments.manager-list', [
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
    public function get(string $attachmentId)
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
     * @throws Exception
     */
    public function delete(string $attachmentId)
    {
        $attachment = $this->attachment->findOrFail($attachmentId);
        $this->checkOwnablePermission('attachment-delete', $attachment);
        $this->attachmentService->deleteFile($attachment);
        return response()->json(['message' => trans('entities.attachments_deleted')]);
    }
}
