<?php

namespace BookStack\Http\Controllers\Api;

use BookStack\Entities\Models\Page;
use BookStack\Exceptions\FileUploadException;
use BookStack\Uploads\Attachment;
use BookStack\Uploads\AttachmentService;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AttachmentApiController extends ApiController
{
    protected $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    /**
     * Get a listing of attachments visible to the user.
     * The external property indicates whether the attachment is simple a link.
     * A false value for the external property would indicate a file upload.
     */
    public function list()
    {
        return $this->apiListingResponse(Attachment::visible(), [
            'id', 'name', 'extension', 'uploaded_to', 'external', 'order', 'created_at', 'updated_at', 'created_by', 'updated_by',
        ]);
    }

    /**
     * Create a new attachment in the system.
     * An uploaded_to value must be provided containing an ID of the page
     * that this upload will be related to.
     *
     * If you're uploading a file the POST data should be provided via
     * a multipart/form-data type request instead of JSON.
     *
     * @throws ValidationException
     * @throws FileUploadException
     */
    public function create(Request $request)
    {
        $this->checkPermission('attachment-create-all');
        $requestData = $this->validate($request, $this->rules()['create']);

        $pageId = $request->get('uploaded_to');
        $page = Page::visible()->findOrFail($pageId);
        $this->checkOwnablePermission('page-update', $page);

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $attachment = $this->attachmentService->saveNewUpload($uploadedFile, $page->id);
        } else {
            $attachment = $this->attachmentService->saveNewFromLink(
                $requestData['name'],
                $requestData['link'],
                $page->id
            );
        }

        $this->attachmentService->updateFile($attachment, $requestData);

        return response()->json($attachment);
    }

    /**
     * Get the details & content of a single attachment of the given ID.
     * The attachment link or file content is provided via a 'content' property.
     * For files the content will be base64 encoded.
     *
     * @throws FileNotFoundException
     */
    public function read(string $id)
    {
        /** @var Attachment $attachment */
        $attachment = Attachment::visible()
            ->with(['createdBy', 'updatedBy'])
            ->findOrFail($id);

        $attachment->setAttribute('links', [
            'html'     => $attachment->htmlLink(),
            'markdown' => $attachment->markdownLink(),
        ]);

        // Simply return a JSON response of the attachment for link-based attachments
        if ($attachment->external) {
            $attachment->setAttribute('content', $attachment->path);

            return response()->json($attachment);
        }

        // Build and split our core JSON, at point of content.
        $splitter = 'CONTENT_SPLIT_LOCATION_' . time() . '_' . rand(1, 40000);
        $attachment->setAttribute('content', $splitter);
        $json = $attachment->toJson();
        $jsonParts = explode($splitter, $json);
        // Get a stream for the file data from storage
        $stream = $this->attachmentService->streamAttachmentFromStorage($attachment);

        return response()->stream(function () use ($jsonParts, $stream) {
            // Output the pre-content JSON data
            echo $jsonParts[0];

            // Stream out our attachment data as base64 content
            stream_filter_append($stream, 'convert.base64-encode', STREAM_FILTER_READ);
            fpassthru($stream);
            fclose($stream);

            // Output our post-content JSON data
            echo $jsonParts[1];
        }, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Update the details of a single attachment.
     * As per the create endpoint, if a file is being provided as the attachment content
     * the request should be formatted as a multipart/form-data request instead of JSON.
     *
     * @throws ValidationException
     * @throws FileUploadException
     */
    public function update(Request $request, string $id)
    {
        $requestData = $this->validate($request, $this->rules()['update']);
        /** @var Attachment $attachment */
        $attachment = Attachment::visible()->findOrFail($id);

        $page = $attachment->page;
        if ($requestData['uploaded_to'] ?? false) {
            $pageId = $request->get('uploaded_to');
            $page = Page::visible()->findOrFail($pageId);
            $attachment->uploaded_to = $requestData['uploaded_to'];
        }

        $this->checkOwnablePermission('page-view', $page);
        $this->checkOwnablePermission('page-update', $page);
        $this->checkOwnablePermission('attachment-update', $attachment);

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $attachment = $this->attachmentService->saveUpdatedUpload($uploadedFile, $attachment);
        }

        $this->attachmentService->updateFile($attachment, $requestData);

        return response()->json($attachment);
    }

    /**
     * Delete an attachment of the given ID.
     *
     * @throws Exception
     */
    public function delete(string $id)
    {
        /** @var Attachment $attachment */
        $attachment = Attachment::visible()->findOrFail($id);
        $this->checkOwnablePermission('attachment-delete', $attachment);

        $this->attachmentService->deleteFile($attachment);

        return response('', 204);
    }

    protected function rules(): array
    {
        return [
            'create' => [
                'name'        => ['required', 'min:1', 'max:255', 'string'],
                'uploaded_to' => ['required', 'integer', 'exists:pages,id'],
                'file'        => array_merge(['required_without:link'], $this->attachmentService->getFileValidationRules()),
                'link'        => ['required_without:file', 'min:1', 'max:255', 'safe_url'],
            ],
            'update' => [
                'name'        => ['min:1', 'max:255', 'string'],
                'uploaded_to' => ['integer', 'exists:pages,id'],
                'file'        => $this->attachmentService->getFileValidationRules(),
                'link'        => ['min:1', 'max:255', 'safe_url'],
            ],
        ];
    }
}
