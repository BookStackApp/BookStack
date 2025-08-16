<?php

namespace BookStack\Uploads\Controllers;

use BookStack\Entities\Queries\PageQueries;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Exceptions\FileUploadException;
use BookStack\Exceptions\NotFoundException;
use BookStack\Http\Controller;
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
            'uploaded_to' => ['required', 'integer', 'exists:pages,id'],
            'file'        => array_merge(['required'], $this->attachmentService->getFileValidationRules()),
        ]);

        $pageId = $request->get('uploaded_to');
        $page = $this->pageQueries->findVisibleByIdOrFail($pageId);

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
     */
    public function getUpdateForm(string $attachmentId)
    {
        /** @var Attachment $attachment */
        $attachment = Attachment::query()->findOrFail($attachmentId);

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

        $this->checkOwnablePermission('page-view', $attachment->page);
        $this->checkOwnablePermission('page-update', $attachment->page);
        $this->checkOwnablePermission('attachment-update', $attachment);

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
     *
     * @throws NotFoundException
     */
    public function attachLink(Request $request)
    {
        $pageId = $request->get('attachment_link_uploaded_to');

        try {
            $this->validate($request, [
                'attachment_link_uploaded_to' => ['required', 'integer', 'exists:pages,id'],
                'attachment_link_name'        => ['required', 'string', 'min:1', 'max:255'],
                'attachment_link_url'         => ['required', 'string', 'min:1', 'max:2000', 'safe_url'],
            ]);
        } catch (ValidationException $exception) {
            return response()->view('attachments.manager-link-form', array_merge($request->only(['attachment_link_name', 'attachment_link_url']), [
                'pageId' => $pageId,
                'errors' => new MessageBag($exception->errors()),
            ]), 422);
        }

        $page = $this->pageQueries->findVisibleByIdOrFail($pageId);

        $this->checkPermission('attachment-create-all');
        $this->checkOwnablePermission('page-update', $page);

        $attachmentName = $request->get('attachment_link_name');
        $link = $request->get('attachment_link_url');
        $this->attachmentService->saveNewFromLink($attachmentName, $link, intval($pageId));

        return view('attachments.manager-link-form', [
            'pageId' => $pageId,
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
        $this->checkOwnablePermission('page-view', $page);

        return view('attachments.manager-list', [
            'attachments' => $page->attachments->all(),
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
        $this->checkOwnablePermission('page-update', $page);

        $attachmentOrder = $request->get('order');
        $this->attachmentService->updateFileOrderWithinPage($attachmentOrder, $pageId);

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

        try {
            $page = $this->pageQueries->findVisibleByIdOrFail($attachment->uploaded_to);
        } catch (NotFoundException $exception) {
            throw new NotFoundException(trans('errors.attachment_not_found'));
        }

        $this->checkOwnablePermission('page-view', $page);

        if ($attachment->external) {
            return redirect($attachment->path);
        }

        $fileName = $attachment->getFileName();
        $attachmentSize = $this->attachmentService->getAttachmentFileSize($attachment);

        // Handle Range Requests for video streaming
        if ($attachment->isVideo() && $request->hasHeader('Range')) {
            return $this->streamVideoWithRange($request, $attachment, $fileName, $attachmentSize);
        }

        $attachmentStream = $this->attachmentService->streamAttachmentFromStorage($attachment);

        if ($request->get('open') === 'true') {
            return $this->download()->streamedInline($attachmentStream, $fileName, $attachmentSize);
        }

        return $this->download()->streamedDirectly($attachmentStream, $fileName, $attachmentSize);
    }

    /**
     * Stream video with Range Request support.
     */
    protected function streamVideoWithRange(Request $request, Attachment $attachment, string $fileName, int $fileSize)
    {
        $range = $request->header('Range');
        $mimeType = $attachment->getVideoMimeType();
        
        // Parse Range header
        if (!preg_match('/bytes=(\d+)-(\d+)?/', $range, $matches)) {
            return response('Invalid Range', 416);
        }

        $start = intval($matches[1]);
        $end = isset($matches[2]) && $matches[2] !== '' ? intval($matches[2]) : $fileSize - 1;
        
        if ($start > $end || $end >= $fileSize) {
            return response('Range Not Satisfiable', 416)
                ->header('Content-Range', "bytes */$fileSize");
        }

        $length = $end - $start + 1;
        
        // Get partial content stream
        $stream = $this->attachmentService->streamAttachmentFromStorage($attachment);
        
        if ($start > 0) {
            fseek($stream, $start);
        }

        return response()->stream(
            function () use ($stream, $length) {
                $remaining = $length;
                $chunkSize = 1024 * 1024; // 1MB chunks
                
                while ($remaining > 0 && !feof($stream)) {
                    $toRead = min($chunkSize, $remaining);
                    echo fread($stream, $toRead);
                    $remaining -= $toRead;
                    flush();
                }
                
                fclose($stream);
            },
            206, // Partial Content
            [
                'Content-Type' => $mimeType,
                'Content-Length' => $length,
                'Accept-Ranges' => 'bytes',
                'Content-Range' => "bytes $start-$end/$fileSize",
                'Cache-Control' => 'no-cache',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]
        );
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
        $this->checkOwnablePermission('attachment-delete', $attachment);
        $this->attachmentService->deleteFile($attachment);

        return response()->json(['message' => trans('entities.attachments_deleted')]);
    }
}
