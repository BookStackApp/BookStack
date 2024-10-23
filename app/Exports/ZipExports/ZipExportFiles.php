<?php

namespace BookStack\Exports\ZipExports;

use BookStack\Uploads\Attachment;
use BookStack\Uploads\AttachmentService;
use BookStack\Uploads\Image;
use BookStack\Uploads\ImageService;
use Illuminate\Support\Str;

class ZipExportFiles
{
    /**
     * References for attachments by attachment ID.
     * @var array<int, string>
     */
    protected array $attachmentRefsById = [];

    /**
     * References for images by image ID.
     * @var array<int, string>
     */
    protected array $imageRefsById = [];

    public function __construct(
        protected AttachmentService $attachmentService,
        protected ImageService $imageService,
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

        $existingFiles = $this->getAllFileNames();
        do {
            $fileName = Str::random(20) . '.' . $attachment->extension;
        } while (in_array($fileName, $existingFiles));

        $this->attachmentRefsById[$attachment->id] = $fileName;

        return $fileName;
    }

    /**
     * Gain a reference to the given image instance.
     * This is expected to be an image that the user has visibility of,
     * no permission/access checks are performed here.
     */
    public function referenceForImage(Image $image): string
    {
        if (isset($this->imageRefsById[$image->id])) {
            return $this->imageRefsById[$image->id];
        }

        $existingFiles = $this->getAllFileNames();
        $extension = pathinfo($image->path, PATHINFO_EXTENSION);
        do {
            $fileName = Str::random(20) . '.' . $extension;
        } while (in_array($fileName, $existingFiles));

        $this->imageRefsById[$image->id] = $fileName;

        return $fileName;
    }

    protected function getAllFileNames(): array
    {
        return array_merge(
            array_values($this->attachmentRefsById),
            array_values($this->imageRefsById),
        );
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

        foreach ($this->imageRefsById as $imageId => $ref) {
            $image = Image::query()->find($imageId);
            $stream = $this->imageService->getImageStream($image);
            $tmpFile = tempnam(sys_get_temp_dir(), 'bszipimage-');
            $tmpFileStream = fopen($tmpFile, 'w');
            stream_copy_to_stream($stream, $tmpFileStream);
            $callback($tmpFile, $ref);
        }
    }
}
