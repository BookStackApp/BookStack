<?php

namespace BookStack\Exports;

use BookStack\Uploads\Attachment;
use BookStack\Uploads\AttachmentService;
use Illuminate\Support\Str;

class ZipExportFiles
{
    /**
     * References for attachments by attachment ID.
     * @var array<int, string>
     */
    protected array $attachmentRefsById = [];

    public function __construct(
        protected AttachmentService $attachmentService,
    ) {
    }

    /**
     * Gain a reference to the given attachment instance.
     * This is expected to be a file-based attachment that the user
     * has visibility of, no permission/access checks are performed here.
     */
    public function referenceForAttachment(Attachment $attachment): string
    {
        if (isset($this->attachmentRefsById[$attachment->id])) {
            return $this->attachmentRefsById[$attachment->id];
        }

        do {
            $fileName = Str::random(20) . '.' . $attachment->extension;
        } while (in_array($fileName, $this->attachmentRefsById));

        $this->attachmentRefsById[$attachment->id] = $fileName;

        return $fileName;
    }

    /**
     * Extract each of the ZIP export tracked files.
     * Calls the given callback for each tracked file, passing a temporary
     * file reference of the file contents, and the zip-local tracked reference.
     */
    public function extractEach(callable $callback): void
    {
        foreach ($this->attachmentRefsById as $attachmentId => $ref) {
            $attachment = Attachment::query()->find($attachmentId);
            $stream = $this->attachmentService->streamAttachmentFromStorage($attachment);
            $tmpFile = tempnam(sys_get_temp_dir(), 'bszipfile-');
            $tmpFileStream = fopen($tmpFile, 'w');
            stream_copy_to_stream($stream, $tmpFileStream);
            $callback($tmpFile, $ref);
        }
    }
}
