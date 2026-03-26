<?php

namespace BookStack\Uploads\Controllers;

use BookStack\Entities\Models\Entity;
use BookStack\Entities\Queries\EntityQueries;
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
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AttachmentController extends Controller
{
    public function __construct(
        protected AttachmentService $attachmentService,
        protected EntityQueries $entityQueries,
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
            'uploaded_to_type' => ['nullable', 'string', Rule::in(Attachment::UPLOAD_TO_ENTITY_TYPES)],
            'file'        => array_merge(['required'], $this->attachmentService->getFileValidationRules()),
        ]);

        $uploadedToId = intval($request->get('uploaded_to'));
        $uploadedToType = $request->get('uploaded_to_type', Attachment::UPLOAD_TO_PAGE);
        $target = $this->findVisibleUploadTarget($uploadedToType, $uploadedToId);

        $this->checkPermission(Permission::AttachmentCreateAll);
        $this->checkUploadTargetPermission($target, 'update');

        $uploadedFile = $request->file('file');

        try {
            $attachment = $this->attachmentService->saveNewUpload($uploadedFile, $uploadedToId, $uploadedToType);
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
        $target = $this->findVisibleUploadTarget($attachment->uploaded_to_type, $attachment->uploaded_to);
        $this->checkUploadTargetPermission($target, 'view');
        $this->checkUploadTargetPermission($target, 'update');
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

        $target = $this->findVisibleUploadTarget($attachment->uploaded_to_type, $attachment->uploaded_to);
        $this->checkUploadTargetPermission($target, 'update');
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

        $target = $this->findVisibleUploadTarget($attachment->uploaded_to_type, $attachment->uploaded_to);
        $this->checkUploadTargetPermission($target, 'view');
        $this->checkUploadTargetPermission($target, 'update');
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
    * Attach a link to an entity.
     *
     * @throws NotFoundException
     */
    public function attachLink(Request $request)
    {
        $uploadedToId = intval($request->get('attachment_link_uploaded_to'));
        $uploadedToType = $request->get('attachment_link_uploaded_to_type', Attachment::UPLOAD_TO_PAGE);

        try {
            $this->validate($request, [
                'attachment_link_uploaded_to' => ['required', 'integer'],
                'attachment_link_uploaded_to_type' => ['nullable', 'string', Rule::in(Attachment::UPLOAD_TO_ENTITY_TYPES)],
                'attachment_link_name'        => ['required', 'string', 'min:1', 'max:255'],
                'attachment_link_url'         => ['required', 'string', 'min:1', 'max:2000', 'safe_url'],
            ]);
        } catch (ValidationException $exception) {
            return response()->view('attachments.manager-link-form', array_merge($request->only(['attachment_link_name', 'attachment_link_url']), [
                'uploadedToId' => $uploadedToId,
                'uploadedToType' => $uploadedToType,
                'errors' => new MessageBag($exception->errors()),
            ]), 422);
        }

        $target = $this->findVisibleUploadTarget($uploadedToType, $uploadedToId);

        $this->checkPermission(Permission::AttachmentCreateAll);
        $this->checkUploadTargetPermission($target, 'update');

        $attachmentName = $request->get('attachment_link_name');
        $link = $request->get('attachment_link_url');
        $this->attachmentService->saveNewFromLink($attachmentName, $link, $uploadedToId, $uploadedToType);

        return view('attachments.manager-link-form', [
            'uploadedToId' => $uploadedToId,
            'uploadedToType' => $uploadedToType,
        ]);
    }

    /**
    * Get the attachments for a specific upload target entity.
     *
     * @throws NotFoundException
     */
    public function listForEntity(string $entityType, int $entityId)
    {
        $entity = $this->findVisibleUploadTarget($entityType, $entityId);
        $attachments = $entity->attachments()->get();

        return view('attachments.manager-list', [
            'attachments' => $attachments,
            'allowInsertLinks' => $entityType === Attachment::UPLOAD_TO_PAGE,
        ]);
    }

    /**
     * Update the attachment sorting.
     *
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function sortForEntity(Request $request, string $entityType, int $entityId)
    {
        $this->validate($request, [
            'order' => ['required', 'array'],
        ]);
        $entity = $this->findVisibleUploadTarget($entityType, $entityId);
        $this->checkUploadTargetPermission($entity, 'update');

        $attachmentOrder = $request->get('order');
        $this->attachmentService->updateFileOrderWithinEntity($attachmentOrder, (string) $entityId, $entityType);

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
        $target = $this->findVisibleUploadTarget($attachment->uploaded_to_type, $attachment->uploaded_to);
        $this->checkUploadTargetPermission($target, 'view');

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

    protected function findVisibleUploadTarget(string $type, int $id): Entity
    {
        if (!in_array($type, Attachment::UPLOAD_TO_ENTITY_TYPES)) {
            throw new NotFoundException(trans('errors.attachment_not_found'));
        }

        $target = $this->entityQueries->findVisibleById($type, $id);

        if ($target === null) {
            throw new NotFoundException(trans('errors.attachment_not_found'));
        }

        return $target;
    }

    protected function checkUploadTargetPermission(Entity $entity, string $action): void
    {
        $permission = match ([$entity->getMorphClass(), $action]) {
            [Attachment::UPLOAD_TO_PAGE, 'view'] => Permission::PageView,
            [Attachment::UPLOAD_TO_PAGE, 'update'] => Permission::PageUpdate,
            [Attachment::UPLOAD_TO_CHAPTER, 'view'] => Permission::ChapterView,
            [Attachment::UPLOAD_TO_CHAPTER, 'update'] => Permission::ChapterUpdate,
            [Attachment::UPLOAD_TO_BOOK, 'view'] => Permission::BookView,
            [Attachment::UPLOAD_TO_BOOK, 'update'] => Permission::BookUpdate,
            default => null,
        };

        if ($permission === null) {
            throw new NotFoundException(trans('errors.attachment_not_found'));
        }

        $this->checkOwnablePermission($permission, $entity);
    }
}
