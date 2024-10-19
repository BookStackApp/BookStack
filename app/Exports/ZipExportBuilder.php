<?php

namespace BookStack\Exports;

use BookStack\Activity\Models\Tag;
use BookStack\Entities\Models\Page;
use BookStack\Exceptions\ZipExportException;
use BookStack\Uploads\Attachment;
use ZipArchive;

class ZipExportBuilder
{
    protected array $data = [];

    public function __construct(
        protected ZipExportFiles $files
    ) {
    }

    /**
     * @throws ZipExportException
     */
    public function buildForPage(Page $page): string
    {
        $this->data['page'] = $this->convertPage($page);
        return $this->build();
    }

    protected function convertPage(Page $page): array
    {
        $tags = array_map($this->convertTag(...), $page->tags()->get()->all());
        $attachments = array_map($this->convertAttachment(...), $page->attachments()->get()->all());

        return [
            'id'          => $page->id,
            'name'        => $page->name,
            'html'        => '', // TODO
            'markdown'    => '', // TODO
            'priority'    => $page->priority,
            'attachments' => $attachments,
            'images'      => [], // TODO
            'tags'        => $tags,
        ];
    }

    protected function convertAttachment(Attachment $attachment): array
    {
        $data = [
            'name'  => $attachment->name,
            'order' => $attachment->order,
        ];

        if ($attachment->external) {
            $data['link'] = $attachment->path;
        } else {
            $data['file'] = $this->files->referenceForAttachment($attachment);
        }

        return $data;
    }

    protected function convertTag(Tag $tag): array
    {
        return [
            'name'  => $tag->name,
            'value' => $tag->value,
            'order' => $tag->order,
        ];
    }

    /**
     * @throws ZipExportException
     */
    protected function build(): string
    {
        $this->data['exported_at'] = date(DATE_ATOM);
        $this->data['instance'] = [
            'version'       => trim(file_get_contents(base_path('version'))),
            'id_ciphertext' => encrypt('bookstack'),
        ];

        $zipFile = tempnam(sys_get_temp_dir(), 'bszip-');
        $zip = new ZipArchive();
        $opened = $zip->open($zipFile, ZipArchive::CREATE);
        if ($opened !== true) {
            throw new ZipExportException('Failed to create zip file for export.');
        }

        $zip->addFromString('data.json', json_encode($this->data));
        $zip->addEmptyDir('files');

        $toRemove = [];
        $this->files->extractEach(function ($filePath, $fileRef) use ($zip, &$toRemove) {
            $zip->addFile($filePath, "files/$fileRef");
            $toRemove[] = $filePath;
        });

        $zip->close();

        foreach ($toRemove as $file) {
            unlink($file);
        }

        return $zipFile;
    }
}
