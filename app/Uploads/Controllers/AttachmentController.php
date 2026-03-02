<?php

namespace BookStack\Uploads\Controllers;

use BookStack\Entities\EntityExistsRule;
use BookStack\Entities\Queries\PageQueries;
use BookStack\Entities\Queries\BookQueries;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Exceptions\FileUploadException;
use BookStack\Exceptions\NotFoundException;
use BookStack\Http\Controller;
use BookStack\Permissions\Permission;
use BookStack\Uploads\Attachment;
use BookStack\Uploads\AttachmentService;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;

class AttachmentController extends Controller
{
    public function __construct(
        protected AttachmentService $attachmentService,
        protected PageQueries $pageQueries,
        protected BookQueries $bookQueries,
        protected PageRepo $pageRepo
    ) {
    }

    /**
     * Endpoint at which attachments are uploaded to.
     *
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function upload(Request $request)
    {
        $this->validate($request, [
            'uploaded_to' => ['required', 'integer'],
            'entity_type' => ['required', 'string', 'in:page,book'],
            'file'        => array_merge(['required'], $this->attachmentService->getFileValidationRules()),
        ]);

        $entityId = $request->get('uploaded_to');
        $entityType = $request->get('entity_type', 'page');
        
        // Validate entity exists and check permissions
        if ($entityType === 'book') {
            $entity = $this->bookQueries->findVisibleByIdOrFail($entityId);
            $this->checkPermission(Permission::AttachmentCreateAll);
            $this->checkOwnablePermission(Permission::BookUpdate, $entity);
        } else {
            $entity = $this->pageQueries->findVisibleByIdOrFail($entityId);
            $this->checkPermission(Permission::AttachmentCreateAll);
            $this->checkOwnablePermission(Permission::PageUpdate, $entity);
        }

        $uploadedFile = $request->file('file');

        try {
            $attachment = $this->attachmentService->saveNewUpload($uploadedFile, $entityId, $entityType);
        } catch (FileUploadException $e) {
            return response($e->getMessage(), 500);
        }

        return response()->json($attachment);
    }

    /**
     * Update an uploaded attachment.
     *
     * @throws ValidationException
     */
    public function uploadUpdate(Request $request, $attachmentId)
    {
        $this->validate($request, [
            'file' => array_merge(['required'], $this->attachmentService->getFileValidationRules()),
        ]);

        /** @var Attachment $attachment */
        $attachment = Attachment::query()->findOrFail($attachmentId);
        $entity = $attachment->attachable;
        
        if (!$entity) {
            throw new NotFoundException(trans('errors.attachment_not_found'));
        }

        if ($attachment->attachable_type === 'BookStack\\Entities\\Models\\Book') {
            $this->checkOwnablePermission(Permission::BookView, $entity);
            $this->checkOwnablePermission(Permission::BookUpdate, $entity);
        } else {
            $this->checkOwnablePermission(Permission::PageView, $entity);
            $this->checkOwnablePermission(Permission::PageUpdate, $entity);
        }
        $this->checkOwnablePermission(Permission::AttachmentUpdate, $attachment);

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
     */
    public function getUpdateForm(string $attachmentId)
    {
        /** @var Attachment $attachment */
        $attachment = Attachment::query()->findOrFail($attachmentId);
        $entity = $attachment->attachable;
        
        if (!$entity) {
            throw new NotFoundException(trans('errors.attachment_not_found'));
        }

        if ($attachment->attachable_type === 'BookStack\\Entities\\Models\\Book') {
            $this->checkOwnablePermission(Permission::BookUpdate, $entity);
        } else {
            $this->checkOwnablePermission(Permission::PageUpdate, $entity);
        }
        $this->checkOwnablePermission(Permission::AttachmentCreate, $attachment);

        return view('attachments.manager-edit-form', [
            'attachment' => $attachment,
        ]);
    }

    /**
     * Update the details of an existing file.
     */
    public function update(Request $request, string $attachmentId)
    {
        /** @var Attachment $attachment */
        $attachment = Attachment::query()->findOrFail($attachmentId);
        $entity = $attachment->attachable;
        
        if (!$entity) {
            throw new NotFoundException(trans('errors.attachment_not_found'));
        }

        try {
            $this->validate($request, [
                'attachment_edit_name' => ['required', 'string', 'min:1', 'max:255'],
                'attachment_edit_url'  => ['string', 'min:1', 'max:2000', 'safe_url'],
            ]);
        } catch (ValidationException $exception) {
            return response()->view('attachments.manager-edit-form', array_merge($request->only(['attachment_edit_name', 'attachment_edit_url']), [
                'attachment' => $attachment,
                'errors'     => new MessageBag($exception->errors()),
            ]), 422);
        }

        if ($attachment->attachable_type === 'BookStack\\Entities\\Models\\Book') {
            $this->checkOwnablePermission(Permission::BookView, $entity);
            $this->checkOwnablePermission(Permission::BookUpdate, $entity);
        } else {
            $this->checkOwnablePermission(Permission::PageView, $entity);
            $this->checkOwnablePermission(Permission::PageUpdate, $entity);
        }
        $this->checkOwnablePermission(Permission::AttachmentUpdate, $attachment);

        $attachment = $this->attachmentService->updateFile($attachment, [
            'name' => $request->get('attachment_edit_name'),
            'link' => $request->get('attachment_edit_url'),
        ]);

        return view('attachments.manager-edit-form', [
            'attachment' => $attachment,
        ]);
    }

    /**
     * Attach a link to a page or book.
     *
     * @throws NotFoundException
     */
    public function attachLink(Request $request)
    {
        $entityId = $request->get('attachment_link_uploaded_to');
        $entityType = $request->get('entity_type', 'page');

        try {
            $validationRules = [
                'attachment_link_uploaded_to' => ['required', 'integer'],
                'attachment_link_name'        => ['required', 'string', 'min:1', 'max:255'],
                'attachment_link_url'         => ['required', 'string', 'min:1', 'max:2000', 'safe_url'],
                'entity_type'                => ['required', 'string', 'in:page,book'],
            ];
            
            if ($entityType === 'page') {
                $validationRules['attachment_link_uploaded_to'][] = new EntityExistsRule('page');
            } else {
                $validationRules['attachment_link_uploaded_to'][] = new EntityExistsRule('book');
            }
            
            $this->validate($request, $validationRules);
        } catch (ValidationException $exception) {
            return response()->view('attachments.manager-link-form', array_merge($request->only(['attachment_link_name', 'attachment_link_url']), [
                'entityId' => $entityId,
                'entityType' => $entityType,
                'errors' => new MessageBag($exception->errors()),
            ]), 422);
        }

        if ($entityType === 'book') {
            $entity = $this->bookQueries->findVisibleByIdOrFail($entityId);
            $this->checkPermission(Permission::AttachmentCreateAll);
            $this->checkOwnablePermission(Permission::BookUpdate, $entity);
        } else {
            $entity = $this->pageQueries->findVisibleByIdOrFail($entityId);
            $this->checkPermission(Permission::AttachmentCreateAll);
            $this->checkOwnablePermission(Permission::PageUpdate, $entity);
        }

        $attachmentName = $request->get('attachment_link_name');
        $link = $request->get('attachment_link_url');
        $this->attachmentService->saveNewFromLink($attachmentName, $link, intval($entityId), $entityType);

        return view('attachments.manager-link-form', [
            'entityId' => $entityId,
            'entityType' => $entityType,
        ]);
    }

    /**
     * Get the attachments for a specific page.
     *
     * @throws NotFoundException
     */
    public function listForPage(int $pageId)
    {
        $page = $this->pageQueries->findVisibleByIdOrFail($pageId);

        return view('attachments.manager-list', [
            'attachments' => $page->attachments->all(),
        ]);
    }

    /**
     * Get the attachments for a specific book.
     *
     * @throws NotFoundException
     */
    public function listForBook(int $bookId)
    {
        $book = $this->bookQueries->findVisibleByIdOrFail($bookId);

        return view('attachments.manager-list', [
            'attachments' => $book->attachments->all(),
        ]);
    }

    /**
     * Update the attachment sorting.
     *
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function sortForPage(Request $request, int $pageId)
    {
        $this->validate($request, [
            'order' => ['required', 'array'],
        ]);
        $page = $this->pageQueries->findVisibleByIdOrFail($pageId);
        $this->checkOwnablePermission(Permission::PageUpdate, $page);

        $attachmentOrder = $request->get('order');
        $this->attachmentService->updateFileOrderWithinPage($attachmentOrder, $pageId);

        return response()->json(['message' => trans('entities.attachments_order_updated')]);
    }

    /**
     * Update the attachment sorting for a book.
     *
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function sortForBook(Request $request, int $bookId)
    {
        $this->validate($request, [
            'order' => ['required', 'array'],
        ]);
        $book = $this->bookQueries->findVisibleByIdOrFail($bookId);
        $this->checkOwnablePermission(Permission::BookUpdate, $book);

        $attachmentOrder = $request->get('order');
        $this->attachmentService->updateFileOrderWithinBook($attachmentOrder, $bookId);

        return response()->json(['message' => trans('entities.attachments_order_updated')]);
    }

    /**
     * Get an attachment from storage.
     *
     * @throws FileNotFoundException
     * @throws NotFoundException
     */
    public function get(Request $request, string $attachmentId)
    {
        /** @var Attachment $attachment */
        $attachment = Attachment::query()->findOrFail($attachmentId);
        $entity = $attachment->attachable;
        
        if (!$entity) {
            throw new NotFoundException(trans('errors.attachment_not_found'));
        }

        // Check permissions based on entity type
        if ($attachment->attachable_type === 'BookStack\\Entities\\Models\\Book') {
            $this->checkOwnablePermission(Permission::BookView, $entity);
        } else {
            $this->checkOwnablePermission(Permission::PageView, $entity);
        }

        if ($attachment->external) {
            return redirect($attachment->path);
        }

        $fileName = $attachment->getFileName();
        $attachmentStream = $this->attachmentService->streamAttachmentFromStorage($attachment);
        $attachmentSize = $this->attachmentService->getAttachmentFileSize($attachment);

        if ($request->get('open') === 'true') {
            return $this->download()->streamedInline($attachmentStream, $fileName, $attachmentSize);
        }

        return $this->download()->streamedDirectly($attachmentStream, $fileName, $attachmentSize);
    }

    /**
     * Delete a specific attachment in the system.
     *
     * @throws Exception
     */
    public function delete(string $attachmentId)
    {
        /** @var Attachment $attachment */
        $attachment = Attachment::query()->findOrFail($attachmentId);
        $this->checkOwnablePermission(Permission::AttachmentDelete, $attachment);
        $this->attachmentService->deleteFile($attachment);

        return response()->json(['message' => trans('entities.attachments_deleted')]);
    }
}
